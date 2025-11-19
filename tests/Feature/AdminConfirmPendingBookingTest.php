<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\BookingManagementService;
use App\Services\PaystackService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminConfirmPendingBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_confirm_booking_with_sufficient_wallet_balance(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'wallet_balance' => 15000,
            'email' => 'customer@example.com',
        ]);

        $car = Car::factory()->create([
            'daily_price' => 5000,
            'is_active' => true,
        ]);

        $neg = ServiceType::create([
            'name' => 'Negotiable Ride',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $neg->id,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'extras' => [],
            'notes' => '',
            'subtotal' => 0,
            'taxes' => 0,
            'total' => 0,
            'status' => 'pending',
        ]);

        $paystackService = $this->createMock(PaystackService::class);
        $service = new BookingManagementService;
        $result = $service->confirmWithWalletCheck($booking, 12000, $paystackService);

        $booking->refresh();
        $user->refresh();

        $this->assertSame('confirmed', $result['status']);
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame(12000.0, (float) $booking->total);
        $this->assertSame(3000.0, (float) $user->wallet_balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => 12000.00,
            'description' => 'Booking charge on admin confirmation',
        ]);
    }

    public function test_admin_can_confirm_booking_with_insufficient_balance_sends_payment_link(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'wallet_balance' => 5000, // Insufficient for 12000 booking
            'email' => 'customer@example.com',
        ]);

        $car = Car::factory()->create([
            'daily_price' => 5000,
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'extras' => [],
            'notes' => '',
            'subtotal' => 0,
            'taxes' => 0,
            'total' => 0,
            'status' => 'pending',
        ]);

        $paystackService = $this->createMock(PaystackService::class);
        $paystackService->expects($this->once())
            ->method('initialize')
            ->with(1200000, 'customer@example.com', $this->anything(), $this->anything())
            ->willReturn([
                'status' => true,
                'authorization_url' => 'https://paystack.com/pay/xyz123',
                'reference' => 'BOOKING_'.$booking->id.'_'.time(),
            ]);

        $service = new BookingManagementService;
        $result = $service->confirmWithWalletCheck($booking, 12000, $paystackService);

        $booking->refresh();
        $user->refresh();

        $this->assertSame('pending_payment', $result['status']);
        $this->assertSame('pending payment', $booking->status);
        $this->assertSame(12000.0, (float) $booking->total);
        $this->assertSame(5000.0, (float) $user->wallet_balance); // Unchanged
        $this->assertNotNull($booking->payment_reference);

        // Verify payment link email was sent
        Mail::assertSent(\App\Mail\BookingPaymentLinkMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_payment_callback_confirms_booking_successfully(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'wallet_balance' => 5000,
            'email' => 'customer@example.com',
        ]);

        $reference = 'BOOKING_TEST_1699123456';

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => Car::factory()->create()->id,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'extras' => [],
            'subtotal' => 12000,
            'taxes' => 0,
            'total' => 12000,
            'status' => 'pending payment',
            'payment_reference' => $reference,
        ]);

        $paystackService = $this->createMock(PaystackService::class);
        $paystackService->expects($this->once())
            ->method('verify')
            ->with($reference)
            ->willReturn([
                'status' => true,
                'amount_kobo' => 1200000,
                'currency' => 'NGN',
                'customer_email' => 'customer@example.com',
                'reference' => $reference,
            ]);

        $service = new BookingManagementService;
        $result = $service->handlePaymentCallback($reference, $paystackService);

        $booking->refresh();

        $this->assertSame('success', $result['status']);
        $this->assertSame('confirmed', $booking->status);
        $this->assertStringContainsString('paystack_payment_', $booking->payment_evidence);
    }
}

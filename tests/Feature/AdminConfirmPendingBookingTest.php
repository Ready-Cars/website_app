<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\BookingManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminConfirmPendingBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_set_price_and_confirm_pending_booking_and_charge_wallet(): void
    {
        $user = User::factory()->create([
            'wallet_balance' => 15000,
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

        // Create pending booking (created previously via negotiable flow)
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

        $service = new BookingManagementService();
        $service->confirmWithPrice($booking, 12000);

        $booking->refresh();
        $user->refresh();

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
}

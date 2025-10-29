<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\BookingManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentEvidenceBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_confirmation_with_payment_evidence(): void
    {
        // Arrange
        Storage::fake('public');

        $user = User::factory()->create(['wallet_balance' => 1000.00]);
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'description' => 'Test service description',
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'status' => 'pending',
            'subtotal' => 500.00,
            'taxes' => 0.00,
            'total' => 500.00,
        ]);

        $file = UploadedFile::fake()->image('payment-receipt.jpg');
        $evidencePath = $file->store('payment-evidence', 'public');

        $service = new BookingManagementService;

        // Act
        $confirmedBooking = $service->confirmWithPrice($booking, 750.00, $evidencePath);

        // Assert
        $this->assertEquals('confirmed', $confirmedBooking->status);
        $this->assertEquals(750.00, $confirmedBooking->total);
        $this->assertEquals($evidencePath, $confirmedBooking->payment_evidence);

        // Check wallet was debited
        $user->refresh();
        $this->assertEquals(250.00, $user->wallet_balance); // 1000 - 750

        // Check wallet transaction was created
        $transaction = WalletTransaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('debit', $transaction->type);
        $this->assertEquals(750.00, $transaction->amount);
        $this->assertEquals(250.00, $transaction->balance_after);
        $this->assertEquals($booking->id, $transaction->meta['booking_id']);

        // Check file was stored
        Storage::disk('public')->assertExists($evidencePath);
    }

    public function test_booking_confirmation_without_payment_evidence_throws_exception(): void
    {
        // Arrange
        $user = User::factory()->create(['wallet_balance' => 1000.00]);
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'description' => 'Test service description',
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'status' => 'pending',
            'subtotal' => 500.00,
            'taxes' => 0.00,
            'total' => 500.00,
        ]);

        $service = new BookingManagementService;

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment evidence is required for booking confirmation.');

        $service->confirmWithPrice($booking, 600.00, '');
    }
}

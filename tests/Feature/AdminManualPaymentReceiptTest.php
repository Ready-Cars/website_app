<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminManualPaymentReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_receipt_and_confirm_manual_payment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'wallet_balance' => 5000,
            'email' => 'customer@example.com',
        ]);

        $car = Car::factory()->create([
            'daily_price' => 5000,
            'is_active' => true,
        ]);

        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'extras' => [],
            'notes' => '',
            'subtotal' => 12000,
            'taxes' => 0,
            'total' => 12000,
            'status' => 'pending payment',
            'payment_reference' => 'MANUAL_' . time(),
        ]);

        // Create a fake receipt file
        $receiptFile = UploadedFile::fake()->image('receipt.jpg', 800, 600);

        // Test the receipt upload and confirmation
        Livewire::test(\App\Livewire\Admin\Bookings::class)
            ->call('openReceiptUpload', $booking->id)
            ->assertSet('receiptUploadOpen', true)
            ->assertSet('viewingId', $booking->id)
            ->set('receiptFile', $receiptFile)
            ->call('confirmManualPaymentWithReceipt')
            ->assertHasNoErrors()
            ->assertSessionHas('success');

        // Refresh booking to check updates
        $booking->refresh();
        $user->refresh();

        // Verify booking was confirmed
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals(12000.0, (float) $booking->total);
        $this->assertNotNull($booking->payment_evidence);
        $this->assertStringStartsWith('payment-evidence/receipt_', $booking->payment_evidence);

        // Verify wallet transactions were created
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => 12000.00,
            'description' => 'Booking funding on admin confirmation',
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => 12000.00,
            'description' => 'Booking charge on admin confirmation',
        ]);

        // Verify file was stored
        Storage::disk('public')->assertExists($booking->payment_evidence);
    }

    public function test_admin_cannot_confirm_payment_without_receipt_file(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'extras' => [],
            'subtotal' => 12000,
            'taxes' => 0,
            'total' => 12000,
            'status' => 'pending payment',
        ]);

        // Test without uploading a file
        Livewire::test(\App\Livewire\Admin\Bookings::class)
            ->call('openReceiptUpload', $booking->id)
            ->call('confirmManualPaymentWithReceipt')
            ->assertHasErrors(['receiptFile']);

        // Verify booking status unchanged
        $booking->refresh();
        $this->assertEquals('pending payment', $booking->status);
        $this->assertNull($booking->payment_evidence);
    }

    public function test_admin_cannot_upload_receipt_for_non_pending_payment_booking(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'extras' => [],
            'subtotal' => 12000,
            'taxes' => 0,
            'total' => 12000,
            'status' => 'confirmed', // Not pending payment
        ]);

        $receiptFile = UploadedFile::fake()->image('receipt.jpg');

        Livewire::test(\App\Livewire\Admin\Bookings::class)
            ->call('openReceiptUpload', $booking->id)
            ->set('receiptFile', $receiptFile)
            ->call('confirmManualPaymentWithReceipt')
            ->assertSessionHas('error', 'This booking is not awaiting payment confirmation');

        // Verify no file was stored
        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertNull($booking->payment_evidence);
    }

    public function test_receipt_upload_validates_file_types_and_size(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'extras' => [],
            'subtotal' => 12000,
            'taxes' => 0,
            'total' => 12000,
            'status' => 'pending payment',
        ]);

        // Test with invalid file type
        $invalidFile = UploadedFile::fake()->create('document.txt', 100);

        Livewire::test(\App\Livewire\Admin\Bookings::class)
            ->call('openReceiptUpload', $booking->id)
            ->set('receiptFile', $invalidFile)
            ->call('confirmManualPaymentWithReceipt')
            ->assertHasErrors(['receiptFile']);

        // Test with oversized file (6MB when limit is 5MB)
        $oversizedFile = UploadedFile::fake()->image('large.jpg')->size(6144);

        Livewire::test(\App\Livewire\Admin\Bookings::class)
            ->call('openReceiptUpload', $booking->id)
            ->set('receiptFile', $oversizedFile)
            ->call('confirmManualPaymentWithReceipt')
            ->assertHasErrors(['receiptFile']);
    }
}

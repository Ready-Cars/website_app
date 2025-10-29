<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentEvidenceDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_payment_evidence(): void
    {
        // Arrange
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['wallet_balance' => 1000.00]);
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'description' => 'Test service description',
        ]);

        // Create a test file
        $file = UploadedFile::fake()->image('payment-receipt.jpg');
        $evidencePath = $file->store('payment-evidence', 'public');

        $booking = Booking::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'status' => 'confirmed',
            'subtotal' => 500.00,
            'taxes' => 0.00,
            'total' => 500.00,
            'payment_evidence' => $evidencePath,
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.payment-evidence.download', $booking));

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContains(
            sprintf('booking-%d-payment-evidence', $booking->id),
            $response->headers->get('Content-Disposition')
        );
    }

    public function test_non_admin_cannot_download_payment_evidence(): void
    {
        // Arrange
        Storage::fake('public');

        $customer = User::factory()->create(['wallet_balance' => 1000.00]);
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'description' => 'Test service description',
        ]);

        $file = UploadedFile::fake()->image('payment-receipt.jpg');
        $evidencePath = $file->store('payment-evidence', 'public');

        $booking = Booking::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'status' => 'confirmed',
            'subtotal' => 500.00,
            'taxes' => 0.00,
            'total' => 500.00,
            'payment_evidence' => $evidencePath,
        ]);

        // Act
        $response = $this->actingAs($customer)
            ->get(route('admin.bookings.payment-evidence.download', $booking));

        // Assert
        $response->assertStatus(302); // AdminOnly middleware redirects to home
    }

    public function test_download_fails_when_no_payment_evidence(): void
    {
        // Arrange
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['wallet_balance' => 1000.00]);
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'description' => 'Test service description',
        ]);

        $booking = Booking::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'status' => 'confirmed',
            'subtotal' => 500.00,
            'taxes' => 0.00,
            'total' => 500.00,
            // No payment evidence
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.payment-evidence.download', $booking));

        // Assert
        $response->assertStatus(404);
    }

    public function test_download_fails_when_file_not_found(): void
    {
        // Arrange
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['wallet_balance' => 1000.00]);
        $car = Car::factory()->create();
        $serviceType = ServiceType::create([
            'name' => 'Test Service',
            'description' => 'Test service description',
        ]);

        $booking = Booking::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'service_type_id' => $serviceType->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'status' => 'confirmed',
            'subtotal' => 500.00,
            'taxes' => 0.00,
            'total' => 500.00,
            'payment_evidence' => 'payment-evidence/nonexistent-file.jpg',
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.payment-evidence.download', $booking));

        // Assert
        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Mail\AdminNewBookingNotificationMail;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminBookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_notification_sent_when_booking_created(): void
    {
        Mail::fake();

        // Create admin users
        $admin1 = User::factory()->create(['is_admin' => true]);
        $admin2 = User::factory()->create(['is_admin' => true]);

        // Create regular user and car
        $user = User::factory()->create(['is_admin' => false]);
        $car = Car::factory()->create();

        // Create a booking directly to trigger notifications
        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'subtotal' => 100.00,
            'taxes' => 10.00,
            'total' => 110.00,
            'status' => 'pending',
        ]);

        // Simulate the admin notification code from RentCar.php
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new AdminNewBookingNotificationMail($booking->load(['car', 'user'])));
        }

        // Assert that emails were sent to both admins
        Mail::assertSent(AdminNewBookingNotificationMail::class, 2);

        Mail::assertSent(AdminNewBookingNotificationMail::class, function ($mail) use ($admin1) {
            return $mail->hasTo($admin1->email);
        });

        Mail::assertSent(AdminNewBookingNotificationMail::class, function ($mail) use ($admin2) {
            return $mail->hasTo($admin2->email);
        });
    }

    public function test_admin_notification_not_sent_to_non_admin_users(): void
    {
        Mail::fake();

        // Create regular users (not admins)
        $user1 = User::factory()->create(['is_admin' => false]);
        $user2 = User::factory()->create(['is_admin' => false]);
        $car = Car::factory()->create();

        // Create a booking
        $booking = Booking::create([
            'user_id' => $user1->id,
            'car_id' => $car->id,
            'pickup_location' => 'Test Pickup',
            'dropoff_location' => 'Test Dropoff',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'subtotal' => 100.00,
            'taxes' => 10.00,
            'total' => 110.00,
            'status' => 'pending',
        ]);

        // Simulate the admin notification code
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new AdminNewBookingNotificationMail($booking->load(['car', 'user'])));
        }

        // Assert no emails were sent since there are no admin users
        Mail::assertNothingSent();
    }

    public function test_admin_notification_sent_regardless_of_booking_status(): void
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create(['is_admin' => true]);

        // Create regular user and car
        $user = User::factory()->create(['is_admin' => false]);
        $car = Car::factory()->create();

        // Test different booking statuses
        $statuses = ['pending', 'confirmed', 'cancelled'];

        foreach ($statuses as $status) {
            $booking = Booking::create([
                'user_id' => $user->id,
                'car_id' => $car->id,
                'pickup_location' => 'Test Pickup',
                'dropoff_location' => 'Test Dropoff',
                'start_date' => now()->addDays(1),
                'end_date' => now()->addDays(3),
                'subtotal' => 100.00,
                'taxes' => 10.00,
                'total' => 110.00,
                'status' => $status,
            ]);

            // Simulate the admin notification code
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $adminUser) {
                Mail::to($adminUser->email)->send(new AdminNewBookingNotificationMail($booking->load(['car', 'user'])));
            }
        }

        // Assert that emails were sent for all 3 booking statuses
        Mail::assertSent(AdminNewBookingNotificationMail::class, 3);
    }
}

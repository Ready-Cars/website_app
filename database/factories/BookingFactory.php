<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+30 days');
        $endDate = $this->faker->dateTimeBetween($startDate->format('Y-m-d H:i:s'), $startDate->format('Y-m-d H:i:s') . ' +7 days');

        return [
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'service_type_id' => null, // Make this optional since ServiceType might not have factory
            'pickup_location' => $this->faker->address(),
            'dropoff_location' => $this->faker->address(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'extras' => [],
            'notes' => $this->faker->optional()->sentence(),
            'subtotal' => $subtotal = $this->faker->randomFloat(2, 50, 500),
            'taxes' => $taxes = round($subtotal * 0.1, 2),
            'total' => $subtotal + $taxes,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'payment_evidence' => null,
            'payment_reference' => null,
            'cancellation_reason' => null,
        ];
    }
}

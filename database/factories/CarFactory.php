<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        $categories = ['Sedan', 'SUV', 'Electric', 'Luxury', 'Compact'];
        $transmissions = ['Automatic', 'Manual'];
        $fuels = ['Petrol', 'Diesel', 'Electric', 'Hybrid'];

        return [
            'name' => fake()->company() . ' ' . fake()->randomElement(['Model S', 'Model X', 'Civic', 'Corolla', 'A4', 'C-Class']),
            'category' => fake()->randomElement($categories),
            'location' => fake()->city(),
            'description' => fake()->sentence(12),
            'image_url' => 'https://picsum.photos/seed/' . fake()->uuid() . '/800/450',
            'daily_price' => fake()->randomFloat(2, 25, 250),
            'seats' => fake()->numberBetween(2, 7),
            'transmission' => fake()->randomElement($transmissions),
            'fuel_type' => fake()->randomElement($fuels),
            'featured' => fake()->boolean(30),
        ];
    }
}

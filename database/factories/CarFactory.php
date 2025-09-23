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
            'name' => fake()->randomElement(['BMW', 'Mercedes', 'Audi', 'Toyota', 'Honda', 'Ford', 'Chevrolet', 'Volkswagen', 'Tesla', 'Porsche']) . ' ' . fake()->randomElement(['M3', 'M5', 'GLI', 'CLA', 'S-Class', 'A3', 'RS7', 'Camry', 'Accord', 'Mustang', 'Corvette', 'Golf', 'Model 3', '911']),
            'category' => fake()->randomElement($categories),
            'location' => fake()->city(),
            'description' => fake()->sentence(12),
            'image_url' => fake()->randomElement([
                'https://images.unsplash.com/photo-1494905998402-395d579af36f',
                'https://images.unsplash.com/photo-1503376780353-7e6692767b70',
                'https://images.unsplash.com/photo-1555626906-fcf10d6851b4',
                'https://images.unsplash.com/photo-1583121274602-3e2820c69888',
                'https://images.unsplash.com/photo-1632245889029-e406faaa34cd'
            ]),
            'daily_price' => fake()->randomFloat(2, 25, 250),
            'seats' => fake()->numberBetween(2, 7),
            'transmission' => fake()->randomElement($transmissions),
            'fuel_type' => fake()->randomElement($fuels),
            'featured' => fake()->boolean(30),
        ];
    }
}

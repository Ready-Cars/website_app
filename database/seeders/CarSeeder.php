<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        Car::factory()->count(24)->create();

        // Ensure some featured cars exist
        if (! Car::where('featured', true)->exists()) {
            Car::inRandomOrder()->limit(3)->update(['featured' => true]);
        }
    }
}

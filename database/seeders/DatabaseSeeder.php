<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

                User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'is_admin' => 1,
                ]);

        //        User::factory()->create([
        //            'name' => 'Olurotimi Rabiu',
        //            'email' => 'rotimi@readycars.com',
        //            'is_admin' => 1,
        //        ]);

//        $this->call([
//            CarSeeder::class,
//        ]);
    }
}

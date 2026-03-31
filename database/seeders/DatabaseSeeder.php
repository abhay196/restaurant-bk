<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // You might already be seeding users, or you can do it here
        // User::factory(10)->create();

        // Call the RestaurantSeeder
        $this->call([
            // RestaurantSeeder::class,
            MenuSeeder::class,
            // You can add other seeders here
        ]);
    }
}
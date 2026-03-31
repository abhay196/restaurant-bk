<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 restaurants using the factory
        // Restaurant::factory()->count(20)->create();
        // Menu::factory()->count(20)->create();

        // You could also create specific ones, e.g.:
        // Restaurant::factory()->create([
        //     'name' => 'My Special Restaurant',
        //     'type' => 'veg',
        // ]);
    }
}
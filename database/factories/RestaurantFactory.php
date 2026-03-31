<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\User; // Assuming you have a User model
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Restaurant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();
        
        return [
            // Assuming user_id links to a User.
            // This will create a new User for each Restaurant.
            // If you'd rather assign to existing users, you could use:
            // 'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'user_id' => User::factory(),
            
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => $this->faker->address(),
            'phone' => $this->faker->numerify('##########'), // Generates a 10-digit number
            'description' => $this->faker->paragraph(3),
            'type' => $this->faker->randomElement(['veg', 'non_veg', 'both']),
            'is_available' => $this->faker->boolean(80), // 80% chance of being true
            'image' => $this->faker->imageUrl(640, 480, 'food', true),
        ];
    }
}
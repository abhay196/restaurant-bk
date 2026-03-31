<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true); // two-word item name like "Spicy Pizza"

        return [
            'restaurant_id' => $this->faker->numberBetween(1, 10),
            'item_name' => ucfirst($name),
            'item_description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 50, 500), // between ₹50–₹500
            'is_available' => $this->faker->boolean(80), // 80% chance true
            'image' => $this->faker->imageUrl(640, 480, 'food', true, 'Dish'),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(1),
            'description' => fake()->text,
            'price' => fake()->numberBetween(200000, 500000),
            'image_url' => fake()->imageUrl($width = 200, $height = 200),
        ];
    }
}

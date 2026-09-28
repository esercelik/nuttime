<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'name' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(3),
            'short_description' => fake()->sentence(),
            'main_image' => 'products/'.fake()->uuid().'.jpg',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}

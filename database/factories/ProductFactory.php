<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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

    public function definition()
    {
        return [
            'name' => fake()->word(),
            'detail' => fake()->paragraph(),
            'price' => fake()->numberBetween(100, 1000),
            'stock' => fake()->randomDigit(),
            'discount' => fake()->numberBetween(2, 30),
        ];
    }
}

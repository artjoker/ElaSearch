<?php

namespace Database\Factories\Product;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->unique()->realTextBetween(4, 25),
            'description' => $this->faker->text(100),
            'short_name'  => $this->faker->unique()->realTextBetween(4, 25),
            'slug'        => $this->faker->word,
            'parent_id'   => $this->faker->numberBetween(1,10),
            'is_active'   => $this->faker->boolean,
            'created_at'  => $this->faker->dateTimeThisYear,
            'updated_at'  => $this->faker->dateTimeThisYear,
        ];

    }
}

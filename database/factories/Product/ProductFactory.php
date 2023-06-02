<?php

namespace Database\Factories\Product;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->title,
            'description' => $this->faker->text(100),
            'short_name'  => $this->faker->title,
            'slug'        => $this->faker->slug,
            'category_id' => Category::factory(),
            'brand_id'    => Brand::factory(),
            'price'       => $this->faker->numberBetween(1, 200),
            'rating'      => $this->faker->numberBetween(1, 5),
            'is_active'   => $this->faker->boolean,
        ];
    }
}

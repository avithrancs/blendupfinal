<?php

namespace Database\Factories;

use App\Models\Drink;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Drink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 400, 500),
            'category' => $this->faker->randomElement(['Beer', 'Wine', 'Smoothie', 'Coffee']),
            'image_url' => $this->faker->imageUrl(),
            'is_featured' => $this->faker->boolean(20),
            'description' => $this->faker->sentence(),
        ];
    }
}

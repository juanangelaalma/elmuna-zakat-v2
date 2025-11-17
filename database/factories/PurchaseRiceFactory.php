<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PurchaseRice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRice>
 */
class PurchaseRiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rice_item_id' => RiceItemFactory::new()->create()->id,
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'price_per_kg' => $this->faker->randomFloat(2, 1, 100),
            'created_by' => UserFactory::new()->create()->id,
        ];
    }
}

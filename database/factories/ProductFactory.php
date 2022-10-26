<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'product_name' => $this->faker->company,
            'cost' => $this->faker->randomElement(config('vendingmachine.products.price_ranges')),
            'amount_available' => random_int(2, 60)
        ];
    }

    public function forOwner(User $user): static
    {
        return $this->state(function (array $attributes) use($user) {
            return [
                'seller_id' => $user->id,
            ];
        });
    }
}

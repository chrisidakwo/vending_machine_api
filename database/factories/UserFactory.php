<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => fake()->randomElement([User::ROLE_BUYER, User::ROLE_SELLER]),
            'deposit' => 0,
        ];
    }

    /**
     * @return $this
     */
    public function buyer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::ROLE_BUYER,
            ];
        });
    }

    /**
     * @return $this
     */
    public function seller(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::ROLE_SELLER,
            ];
        });
    }

    public function deposit(int $amount = 0): static
    {
        return $this->state(function (array $attributes) use ($amount) {
            return [
                'deposit' => $attributes['deposit'] + $amount,
            ];
        });
    }
}

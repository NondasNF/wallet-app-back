<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'type' => $this->faker->randomElement(['deposit', 'transfer']),
        ];
    }
}

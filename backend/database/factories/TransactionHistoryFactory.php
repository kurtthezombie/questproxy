<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\TransactionHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionHistory>
 */
class TransactionHistoryFactory extends Factory
{
    protected $model = TransactionHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'status' => $this->faker->randomElement(['pending','completed','failed']),
        ];
    }
}

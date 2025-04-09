<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{

    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payer_id' => 1,
            'amount' => $this->faker->randomFloat(2,10,500),
            'method' => $this->faker->randomElement(['card','gcash','paymaya']),
            'details' => $this->faker->sentence(),
            'transaction_id' => 'TXN' . $this->faker->unique()->randomNumber(6, true),
            'payment_link' => $this->faker->url(),
            'status' => $this->faker->randomElement(['pending','completed','failed']),
            'booking_id' => null,
        ];
    }
}

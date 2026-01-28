<?php

namespace Database\Factories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'USD',
            'donor_name' => $this->faker->name,
            'is_monthly' => $this->faker->boolean,
            'status' => 'completed',
            'payment_method' => 'credit_card',
        ];
    }
}

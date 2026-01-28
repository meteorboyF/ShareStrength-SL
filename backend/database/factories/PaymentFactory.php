<?php

namespace Database\Factories;

use App\Models\Helper;
use App\Models\Payment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'payer_id' => User::factory(),
            'payee_id' => Helper::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'hours_worked' => $this->faker->numberBetween(1, 10),
            'hourly_rate' => $this->faker->numberBetween(15, 50),
            'status' => 'pending', // pending, paid, failed
            'paid_at' => null,
        ];
    }
}

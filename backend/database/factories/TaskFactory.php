<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'budget' => $this->faker->numberBetween(10, 100),
            'location' => $this->faker->address,
            'urgency' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => 'open',
            'required_skills' => ['cleaning', 'cooking'],
        ];
    }
}

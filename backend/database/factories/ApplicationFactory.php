<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Helper;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'helper_id' => Helper::factory(),
            'applicant_type' => 'helper',
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function accepted()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    public function rejected()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}

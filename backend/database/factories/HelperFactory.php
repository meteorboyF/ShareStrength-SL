<?php

namespace Database\Factories;

use App\Models\Helper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HelperFactory extends Factory
{
    protected $model = Helper::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_verified' => true,
            'skills' => 'cooking, cleaning, nursing',
        ];
    }
}

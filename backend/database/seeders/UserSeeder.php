<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create PWD User
        User::create([
            'name' => 'Alice Pwd',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'role' => 'pwd',
            'disability_type' => 'Mobility Impairment',
            'address' => '123 Accessibility Ln',
        ]);

        // Create Caregiver User
        User::create([
            'name' => 'Bob Caregiver',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'role' => 'caregiver',
            'skills' => 'First Aid, Lifting, Driving',
            'address' => '456 Helper St',
        ]);

        // Create Admin (Optional)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}

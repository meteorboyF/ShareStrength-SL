<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Helper;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Alice PWD',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'disability_type' => 'Mobility Impairment',
            'address' => '123 Accessibility Ln',
            'is_active' => true,
        ]);

        Helper::create([
            'name' => 'Bob HelpMate',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'skills' => 'First Aid, Lifting, Driving',
            'address' => '456 Helper St',
            'is_verified' => true,
            'is_active' => true,
        ]);

        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
    }
}

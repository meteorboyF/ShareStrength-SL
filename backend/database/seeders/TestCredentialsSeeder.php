<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Helper;
use App\Models\User;

class TestCredentialsSeeder extends Seeder
{
    public function run(): void
    {
        // Create Standard User (PWD)
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'John User',
                'password' => Hash::make('password'),
                'profile_photo' => 'https://ui-avatars.com/api/?name=John+User&background=6D28D9&color=fff',
                'is_active' => true,
            ]
        );

        // Create HelpMate (Caregiver)
        Helper::firstOrCreate(
            ['email' => 'helpmate@example.com'],
            [
                'name' => 'Jane HelpMate',
                'password' => Hash::make('password'),
                'skills' => 'Mobility Support, Cooking, Companionship',
                'profile_photo' => 'https://ui-avatars.com/api/?name=Jane+HelpMate&background=10B981&color=fff',
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        // Create Admin
        Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}

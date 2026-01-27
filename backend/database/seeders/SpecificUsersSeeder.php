<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Helper;
use Illuminate\Support\Facades\Hash;

class SpecificUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create User
        if (!User::where('email', 'user1@gmail.com')->exists()) {
            User::create([
                'name' => 'Test User',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('12345678'),
                'phone' => '1234567890',
                'address' => '123 User St',
                'location' => 'New York, NY',
                'profile_photo_url' => 'https://placehold.co/150',
                'is_active' => true,
            ]);
            $this->command->info('User "user1@gmail.com" created.');
        } else {
            $this->command->info('User "user1@gmail.com" already exists.');
        }

        // Create Helper
        if (!Helper::where('email', 'helper@gmail.com')->exists()) {
            Helper::create([
                'name' => 'Test Helper',
                'email' => 'helper@gmail.com',
                'password' => Hash::make('12345678'),
                'phone' => '0987654321',
                'address' => '456 Helper Ln',
                'location' => 'Los Angeles, CA',
                'skills' => json_encode(['Nursing', 'First Aid']),
                'bio' => 'Experienced helper ready to assist.',
                'profile_photo_url' => 'https://placehold.co/150',
                'is_verified' => true,
                'is_active' => true,
            ]);
            $this->command->info('Helper "helper@gmail.com" created.');
        } else {
            $this->command->info('Helper "helper@gmail.com" already exists.');
        }
    }
}

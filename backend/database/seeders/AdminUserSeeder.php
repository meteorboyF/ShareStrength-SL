<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = Admin::where('email', 'admin@sharength.com')->exists();

        if (!$adminExists) {
            Admin::create([
                'name' => 'Admin User',
                'email' => 'admin@sharength.com',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@sharength.com');
            $this->command->info('Password: Admin123!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}

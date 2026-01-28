<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin account
        Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin@gmail.com'),
                'is_active' => true,
            ]
        );

        echo "✅ Admin account created successfully!\n";
        echo "Email: admin@gmail.com\n";
        echo "Password: admin@gmail.com\n";
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class CreateOpenTasksSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('email', 'user1@gmail.com')->first();

        if (!$creator) {
            $this->command->error('User user1@gmail.com not found. Please run SpecificUsersSeeder first.');
            return;
        }

        Task::create([
            'created_by' => $creator->id,
            'title' => 'Help with Groceries',
            'description' => 'Need assistance shopping for weekly groceries at Whole Foods.',
            'budget' => 25.00,
            'location' => 'Downtown Market',
            'required_skills' => json_encode(['Shopping', 'Driving']),
            'urgency' => 'medium',
            'status' => 'open',
        ]);

        Task::create([
            'created_by' => $creator->id,
            'title' => 'Morning Walk Companion',
            'description' => 'Looking for someone to accompany me for a 30-minute walk in the park.',
            'budget' => 15.00,
            'location' => 'Central Park',
            'required_skills' => json_encode(['Companionship']),
            'urgency' => 'low',
            'status' => 'open',
        ]);

        Task::create([
            'created_by' => $creator->id,
            'title' => 'Tech Support for Printer',
            'description' => 'My printer is not connecting to WiFi. Need someone tech-savvy.',
            'budget' => 30.00,
            'location' => 'Home',
            'required_skills' => json_encode(['Tech Support']),
            'urgency' => 'high',
            'status' => 'open',
        ]);

        $this->command->info('Created 3 open tasks for ' . $creator->email);
    }
}

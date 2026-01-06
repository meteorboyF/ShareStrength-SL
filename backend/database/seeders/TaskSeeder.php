<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice@example.com')->first();

        if ($alice) {
            Task::create([
                'created_by' => $alice->id,
                'title' => 'Grocery Shopping Help',
                'description' => 'I need someone to help me carry groceries from the store to my apartment.',
                'location' => '123 Accessibility Ln',
                'budget' => 25.00,
                'status' => 'open',
                'required_skills' => ['Lifting', 'Driving'],
            ]);

            Task::create([
                'created_by' => $alice->id,
                'title' => 'Doctor Appointment Ride',
                'description' => 'Need a ride to the clinic.',
                'location' => 'City Hospital',
                'budget' => 15.00,
                'status' => 'open',
                'required_skills' => ['Driving'],
            ]);
        }
    }
}

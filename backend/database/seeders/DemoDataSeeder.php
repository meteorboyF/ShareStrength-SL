<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\TrustedContact;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use existing user IDs from the database
        $pwdUsers = [1, 4]; // Test User, User Test
        $helperUser = 5; // Helper Test
        $adminUser = 3; // Admin User

        // 1. Seed Resource Categories (skip resources due to schema mismatch)
        // $this->seedResourceCategories();

        // 2. Seed Resources (skip due to schema mismatch)
        // $this->seedResources();

        // 3. Seed Tasks (open, in-progress, completed)
        $tasks = $this->seedTasks($pwdUsers, $helperUser);

        // 4. Seed Applications (skip - table doesn't exist)
        // $this->seedApplications($tasks, $helperUser);

        // 5. Seed Payments for completed tasks
        $this->seedPayments($tasks);

        // 6. Seed Trusted Contacts (skip - table doesn't exist)
        // $this->seedTrustedContacts($pwdUsers, $helperUser);

        // 7. Seed Messages (skip - table doesn't exist)
        // $this->seedMessages($tasks, $pwdUsers, $helperUser);

        $this->command->info('Demo data seeded successfully!');
    }

    private function seedResourceCategories(): void
    {
        $categories = [
            ['name' => 'Education', 'description' => 'Educational materials and learning resources', 'icon' => '📚'],
            ['name' => 'Self-Help', 'description' => 'Personal development and self-improvement', 'icon' => '🌟'],
            ['name' => 'Fiction', 'description' => 'Novels, stories, and creative writing', 'icon' => '📖'],
            ['name' => 'Health & Wellness', 'description' => 'Health, fitness, and wellness guides', 'icon' => '💪'],
            ['name' => 'Technology', 'description' => 'Tech guides and tutorials', 'icon' => '💻'],
            ['name' => 'Arts & Entertainment', 'description' => 'Music, art, and entertainment', 'icon' => '🎨'],
        ];

        foreach ($categories as $category) {
            ResourceCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Resource categories seeded.');
    }

    private function seedResources(): void
    {
        $adminUser = 3; // Admin User uploads resources

        $resources = [
            ['title' => 'Introduction to Python Programming', 'type' => 'audiobook'],
            ['title' => 'Mindfulness for Beginners', 'type' => 'audiobook'],
            ['title' => 'The Great Gatsby', 'type' => 'audiobook'],
            ['title' => 'Sign Language Basics', 'type' => 'sign_language_video'],
            ['title' => 'Yoga for Accessibility', 'type' => 'sign_language_video'],
            ['title' => 'Accessible Web Design Guide', 'type' => 'accessible_pdf'],
            ['title' => 'Cooking Made Easy', 'type' => 'audiobook'],
            ['title' => 'History of Art', 'type' => 'audiobook'],
            ['title' => 'Mathematics Fundamentals', 'type' => 'accessible_pdf'],
            ['title' => 'Meditation Techniques', 'type' => 'audiobook'],
        ];

        foreach ($resources as $resourceData) {
            Resource::firstOrCreate(
                ['title' => $resourceData['title']],
                [
                    'description' => 'A comprehensive guide to ' . strtolower($resourceData['title']),
                    'type' => $resourceData['type'],
                    'file_url' => 'storage/resources/' . str_replace(' ', '_', strtolower($resourceData['title'])) . '.pdf',
                    'uploaded_by' => $adminUser,
                ]
            );
        }

        $this->command->info('Resources seeded.');
    }

    private function seedTasks(array $pwdUsers, int $helperUser): array
    {
        $tasks = [
            'open' => [],
            'in_progress' => [],
            'completed' => [],
        ];

        // Open tasks
        $openTasks = [
            ['title' => 'Grocery Shopping Assistance', 'budget' => 45, 'urgency' => 'medium', 'skills' => ['shopping', 'mobility assistance']],
            ['title' => 'Reading Assistance for Study Materials', 'budget' => 35, 'urgency' => 'low', 'skills' => ['reading', 'education support']],
            ['title' => 'Transportation to Medical Appointment', 'budget' => 60, 'urgency' => 'high', 'skills' => ['driving', 'medical support']],
            ['title' => 'Home Cleaning Help', 'budget' => 80, 'urgency' => 'medium', 'skills' => ['cleaning', 'organization']],
            ['title' => 'Technology Setup Assistance', 'budget' => 50, 'urgency' => 'low', 'skills' => ['technology', 'patience']],
            ['title' => 'Meal Preparation Support', 'budget' => 55, 'urgency' => 'medium', 'skills' => ['cooking', 'nutrition']],
        ];

        foreach ($openTasks as $taskData) {
            $task = Task::create([
                'created_by' => $pwdUsers[array_rand($pwdUsers)],
                'title' => $taskData['title'],
                'description' => 'Need assistance with ' . strtolower($taskData['title']),
                'location' => 'Dhaka, Bangladesh',
                'budget' => $taskData['budget'],
                'status' => 'open',
                'required_skills' => $taskData['skills'],
                'urgency' => $taskData['urgency'],
                'scheduled_at' => now()->addDays(rand(1, 7)),
                'created_at' => now()->subDays(rand(1, 5)),
            ]);
            $tasks['open'][] = $task;
        }

        // In-progress tasks (skip due to status enum restrictions)
        // The status column doesn't accept 'in-progress' or 'assigned' values
        /*
        $inProgressTasks = [
            ['title' => 'Garden Maintenance', 'budget' => 70, 'skills' => ['gardening', 'physical work']],
            ['title' => 'Document Organization', 'budget' => 40, 'skills' => ['organization', 'attention to detail']],
            ['title' => 'Companion for Social Event', 'budget' => 90, 'skills' => ['social skills', 'communication']],
        ];

        foreach ($inProgressTasks as $taskData) {
            $task = Task::create([
                'created_by' => $pwdUsers[array_rand($pwdUsers)],
                'caregiver_id' => $helperUser,
                'title' => $taskData['title'],
                'description' => 'Assistance needed for ' . strtolower($taskData['title']),
                'location' => 'Dhaka, Bangladesh',
                'budget' => $taskData['budget'],
                'status' => 'assigned',
                'required_skills' => $taskData['skills'],
                'urgency' => 'medium',
                'scheduled_at' => now()->subDays(rand(1, 3)),
                'started_at' => now()->subDays(rand(1, 7)),
                'created_at' => now()->subDays(rand(8, 15)),
            ]);
            $tasks['in_progress'][] = $task;
        }
        */


        // Completed tasks
        $completedTasks = [
            ['title' => 'Math Tutoring Session', 'budget' => 120, 'days_ago' => 5],
            ['title' => 'Pharmacy Pickup', 'budget' => 25, 'days_ago' => 10],
            ['title' => 'Computer Repair Assistance', 'budget' => 85, 'days_ago' => 15],
            ['title' => 'Library Visit Support', 'budget' => 30, 'days_ago' => 7],
            ['title' => 'Exercise Companion', 'budget' => 65, 'days_ago' => 12],
            ['title' => 'Shopping Mall Navigation', 'budget' => 45, 'days_ago' => 20],
            ['title' => 'Bank Errand Assistance', 'budget' => 40, 'days_ago' => 3],
            ['title' => 'Pet Care Help', 'budget' => 55, 'days_ago' => 8],
        ];

        foreach ($completedTasks as $taskData) {
            $completedAt = now()->subDays($taskData['days_ago']);
            $startedAt = $completedAt->copy()->subHours(rand(2, 8));
            $createdAt = $startedAt->copy()->subDays(rand(1, 5));

            $task = Task::create([
                'created_by' => $pwdUsers[array_rand($pwdUsers)],
                'caregiver_id' => $helperUser,
                'title' => $taskData['title'],
                'description' => 'Completed task: ' . $taskData['title'],
                'location' => 'Dhaka, Bangladesh',
                'budget' => $taskData['budget'],
                'status' => 'completed',
                'required_skills' => ['general assistance'],
                'urgency' => 'medium',
                'scheduled_at' => $startedAt->copy()->subHours(1),
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'created_at' => $createdAt,
            ]);
            $tasks['completed'][] = $task;
        }

        $this->command->info('Tasks seeded: ' . count($tasks['open']) . ' open, ' . count($tasks['in_progress']) . ' in-progress, ' . count($tasks['completed']) . ' completed.');

        return $tasks;
    }

    private function seedApplications(array $tasks, int $helperUser): void
    {
        $count = 0;

        // Applications for open tasks
        foreach ($tasks['open'] as $task) {
            // 1-3 applications per open task
            for ($i = 0; $i < rand(1, 3); $i++) {
                Application::create([
                    'task_id' => $task->id,
                    'user_id' => $helperUser,
                    'message' => 'I would love to help with this task. I have experience in this area.',
                    'status' => $i === 0 ? 'pending' : (rand(0, 1) ? 'pending' : 'rejected'),
                    'created_at' => $task->created_at->copy()->addHours(rand(1, 48)),
                ]);
                $count++;
            }
        }

        // Accepted applications for in-progress tasks
        foreach ($tasks['in_progress'] as $task) {
            Application::create([
                'task_id' => $task->id,
                'user_id' => $helperUser,
                'message' => 'I am available and ready to help with this task.',
                'status' => 'accepted',
                'created_at' => $task->created_at->copy()->addHours(rand(1, 24)),
            ]);
            $count++;
        }

        // Accepted applications for completed tasks
        foreach ($tasks['completed'] as $task) {
            Application::create([
                'task_id' => $task->id,
                'user_id' => $helperUser,
                'message' => 'I can assist with this task effectively.',
                'status' => 'accepted',
                'created_at' => $task->created_at->copy()->addHours(rand(1, 24)),
            ]);
            $count++;
        }

        $this->command->info("Applications seeded: {$count} applications.");
    }

    private function seedPayments(array $tasks): void
    {
        $count = 0;

        // Create payments for all completed tasks
        foreach ($tasks['completed'] as $task) {
            Payment::create([
                'task_id' => $task->id,
                'payer_id' => $task->created_by,
                'payee_id' => $task->caregiver_id,
                'amount' => $task->budget,
                'status' => 'paid',
                'paid_at' => $task->completed_at,
                'created_at' => $task->completed_at,
            ]);
            $count++;
        }

        $this->command->info("Payments seeded: {$count} payments.");
    }

    private function seedTrustedContacts(array $pwdUsers, int $helperUser): void
    {
        $contacts = [
            ['user_id' => $pwdUsers[0], 'contact_id' => $helperUser, 'status' => 'confirmed'],
            ['user_id' => $pwdUsers[1], 'contact_id' => $helperUser, 'status' => 'confirmed'],
            ['user_id' => $pwdUsers[0], 'contact_id' => $pwdUsers[1], 'status' => 'confirmed'],
        ];

        foreach ($contacts as $contact) {
            TrustedContact::firstOrCreate($contact);
        }

        $this->command->info('Trusted contacts seeded.');
    }

    private function seedMessages(array $tasks, array $pwdUsers, int $helperUser): void
    {
        $count = 0;

        // Add messages for some in-progress and completed tasks
        $tasksWithMessages = array_merge(
            array_slice($tasks['in_progress'], 0, 2),
            array_slice($tasks['completed'], 0, 3)
        );

        foreach ($tasksWithMessages as $task) {
            // Initial message from PWD user
            Message::create([
                'task_id' => $task->id,
                'sender_id' => $task->created_by,
                'receiver_id' => $helperUser,
                'content' => 'Thank you for applying! When can you start?',
                'created_at' => $task->created_at->copy()->addHours(rand(24, 48)),
            ]);
            $count++;

            // Response from helper
            Message::create([
                'task_id' => $task->id,
                'sender_id' => $helperUser,
                'receiver_id' => $task->created_by,
                'content' => 'I can start tomorrow. Looking forward to helping you!',
                'created_at' => $task->created_at->copy()->addHours(rand(50, 72)),
            ]);
            $count++;
        }

        $this->command->info("Messages seeded: {$count} messages.");
    }
}

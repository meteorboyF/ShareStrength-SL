<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;
use App\Models\Payment;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting comprehensive data seeding...');

        // Get test users
        $user = User::where('email', 'user@example.com')->first();
        $helpmate = User::where('email', 'helpmate@example.com')->first();

        if (!$user || !$helpmate) {
            $this->command->error('Test users not found! Please run DatabaseSeeder first.');
            return;
        }

        // Clear existing data
        Resource::truncate();

        // Seed comprehensive resources
        $this->seedResources();

        // Seed realistic payment history
        $this->seedPayments($user, $helpmate);

        $this->command->info('Comprehensive data seeded successfully!');
    }

    private function seedResources()
    {
        $resources = [
            // Accessibility Guides
            [
                'title' => 'Complete Guide to Home Accessibility Modifications',
                'description' => 'A comprehensive 50-page guide covering wheelchair ramps, grab bars, doorway widening, and bathroom modifications. Includes cost estimates and contractor recommendations.',
                'type' => 'pdf',
                'category' => 'Accessibility Guides',
                'url' => 'https://www.ada.gov/resources/home-modifications.pdf',
            ],
            [
                'title' => 'Assistive Technology for Daily Living',
                'description' => 'Video series demonstrating the latest assistive devices for cooking, dressing, bathing, and mobility. Features product reviews and where to purchase.',
                'type' => 'video',
                'category' => 'Accessibility Guides',
                'url' => 'https://www.youtube.com/watch?v=assistive-tech-demo',
            ],
            [
                'title' => 'Smart Home Automation for Accessibility',
                'description' => 'Learn how to set up voice-controlled lights, thermostats, door locks, and appliances. Perfect for individuals with limited mobility.',
                'type' => 'pdf',
                'category' => 'Technology',
                'url' => 'https://www.accessibility.com/smart-home-guide.pdf',
            ],

            // Health & Wellness
            [
                'title' => 'Mental Health Resources for Seniors and PWDs',
                'description' => 'Directory of mental health professionals specializing in geriatric care and disability support. Includes telehealth options and support groups.',
                'type' => 'pdf',
                'category' => 'Health & Wellness',
                'url' => 'https://www.mentalhealth.gov/senior-resources.pdf',
            ],
            [
                'title' => 'Nutrition and Meal Planning for Special Diets',
                'description' => 'Nutritional guidelines for diabetes, heart disease, and kidney disease. Includes 30 easy-to-prepare recipes and grocery shopping tips.',
                'type' => 'pdf',
                'category' => 'Health & Wellness',
                'url' => 'https://www.nutrition.gov/special-diets.pdf',
            ],
            [
                'title' => 'Chair Yoga and Gentle Exercise Routines',
                'description' => '20-minute video series of low-impact exercises designed for seniors and individuals with mobility limitations. Improves flexibility and strength.',
                'type' => 'video',
                'category' => 'Health & Wellness',
                'url' => 'https://www.youtube.com/watch?v=chair-yoga-seniors',
            ],
            [
                'title' => 'Medication Management Guide',
                'description' => 'Best practices for organizing medications, setting reminders, and understanding drug interactions. Includes printable medication tracker.',
                'type' => 'pdf',
                'category' => 'Health & Wellness',
                'url' => 'https://www.health.gov/medication-management.pdf',
            ],

            // Legal & Financial
            [
                'title' => 'Understanding Social Security Disability Benefits (SSDI)',
                'description' => 'Step-by-step guide to applying for SSDI, eligibility requirements, and what to expect during the application process. Updated for 2026.',
                'type' => 'pdf',
                'category' => 'Legal & Financial',
                'url' => 'https://www.ssa.gov/disability-benefits-guide.pdf',
            ],
            [
                'title' => 'Medicare and Medicaid Explained',
                'description' => 'Clear explanations of coverage options, enrollment periods, and how to maximize your benefits. Includes state-specific information.',
                'type' => 'pdf',
                'category' => 'Legal & Financial',
                'url' => 'https://www.medicare.gov/coverage-guide.pdf',
            ],
            [
                'title' => 'Estate Planning and Power of Attorney',
                'description' => 'Essential legal documents every senior should have, including wills, living wills, and healthcare proxies. Includes templates and lawyer referrals.',
                'type' => 'pdf',
                'category' => 'Legal & Financial',
                'url' => 'https://www.legalaid.org/estate-planning.pdf',
            ],
            [
                'title' => 'Financial Assistance Programs Directory',
                'description' => 'Comprehensive list of federal, state, and local programs offering financial help for housing, utilities, food, and healthcare.',
                'type' => 'pdf',
                'category' => 'Legal & Financial',
                'url' => 'https://www.benefits.gov/assistance-programs.pdf',
            ],

            // Technology
            [
                'title' => 'Smartphone Basics for Seniors',
                'description' => 'Easy-to-follow video tutorials on using smartphones for calls, texts, video chats, and emergency alerts. Covers both iPhone and Android.',
                'type' => 'video',
                'category' => 'Technology',
                'url' => 'https://www.youtube.com/watch?v=smartphone-seniors-guide',
            ],
            [
                'title' => 'Telehealth: How to Have Virtual Doctor Visits',
                'description' => 'Guide to setting up and using telehealth platforms like Zoom, Teladoc, and MyChart. Includes troubleshooting tips.',
                'type' => 'pdf',
                'category' => 'Technology',
                'url' => 'https://www.telehealth.gov/patient-guide.pdf',
            ],
            [
                'title' => 'Online Safety and Scam Prevention',
                'description' => 'Protect yourself from phishing, identity theft, and online scams. Learn to recognize warning signs and secure your accounts.',
                'type' => 'pdf',
                'category' => 'Technology',
                'url' => 'https://www.ftc.gov/online-safety-seniors.pdf',
            ],

            // Community Support
            [
                'title' => 'Local Support Groups and Community Centers',
                'description' => 'Find support groups for caregivers, chronic illness, grief, and more. Includes meeting schedules and contact information.',
                'type' => 'pdf',
                'category' => 'Community Support',
                'url' => 'https://www.communityresources.org/support-groups.pdf',
            ],
            [
                'title' => 'Transportation Services for Seniors and PWDs',
                'description' => 'Directory of paratransit, volunteer driver programs, and accessible taxi services in your area. Includes eligibility and booking info.',
                'type' => 'pdf',
                'category' => 'Community Support',
                'url' => 'https://www.transportation.gov/senior-services.pdf',
            ],
            [
                'title' => 'Volunteer Opportunities and Social Activities',
                'description' => 'Stay active and engaged! List of volunteer programs, hobby clubs, and social events welcoming seniors and individuals with disabilities.',
                'type' => 'pdf',
                'category' => 'Community Support',
                'url' => 'https://www.volunteer.gov/senior-opportunities.pdf',
            ],
            [
                'title' => 'Emergency Preparedness for PWDs',
                'description' => 'Create a personalized emergency plan including evacuation routes, medication lists, and emergency contacts. Includes printable checklist.',
                'type' => 'pdf',
                'category' => 'Community Support',
                'url' => 'https://www.ready.gov/disability-preparedness.pdf',
            ],
        ];

        foreach ($resources as $resource) {
            Resource::create($resource);
        }

        $this->command->info('✓ Seeded ' . count($resources) . ' resources');
    }

    private function seedPayments($user, $helpmate)
    {
        // Create multiple completed tasks with payments
        $tasks = [
            [
                'title' => 'Grocery Shopping Assistance',
                'budget' => 45.00,
                'days_ago' => 25,
            ],
            [
                'title' => 'House Cleaning',
                'budget' => 80.00,
                'days_ago' => 18,
            ],
            [
                'title' => 'Doctor Appointment Transportation',
                'budget' => 35.00,
                'days_ago' => 12,
            ],
            [
                'title' => 'Yard Work and Gardening',
                'budget' => 60.00,
                'days_ago' => 8,
            ],
            [
                'title' => 'Medication Pickup',
                'budget' => 25.00,
                'days_ago' => 5,
            ],
            [
                'title' => 'Light Home Repairs',
                'budget' => 95.00,
                'days_ago' => 3,
            ],
        ];

        foreach ($tasks as $taskData) {
            $task = Task::create([
                'created_by' => $user->id,
                'caregiver_id' => $helpmate->id,
                'title' => $taskData['title'],
                'description' => 'Completed task: ' . $taskData['title'],
                'location' => '123 Main St, City, State',
                'budget' => $taskData['budget'],
                'status' => 'completed',
                'urgency' => 'medium',
                'scheduled_at' => Carbon::now()->subDays($taskData['days_ago']),
                'created_at' => Carbon::now()->subDays($taskData['days_ago'] + 2),
            ]);

            Payment::create([
                'task_id' => $task->id,
                'payer_id' => $user->id,
                'payee_id' => $helpmate->id,
                'amount' => $taskData['budget'],
                'status' => 'paid',
                'paid_at' => Carbon::now()->subDays($taskData['days_ago'] - 1),
                'created_at' => Carbon::now()->subDays($taskData['days_ago']),
            ]);
        }

        // Add some pending payments
        $pendingTasks = [
            [
                'title' => 'Weekly Meal Preparation',
                'budget' => 55.00,
            ],
            [
                'title' => 'Companionship Visit',
                'budget' => 40.00,
            ],
        ];

        foreach ($pendingTasks as $taskData) {
            $task = Task::create([
                'created_by' => $user->id,
                'caregiver_id' => $helpmate->id,
                'title' => $taskData['title'],
                'description' => 'In progress: ' . $taskData['title'],
                'location' => '123 Main St, City, State',
                'budget' => $taskData['budget'],
                'status' => 'accepted',
                'urgency' => 'medium',
                'scheduled_at' => Carbon::now(),
                'created_at' => Carbon::now()->subDays(1),
            ]);

            Payment::create([
                'task_id' => $task->id,
                'payer_id' => $user->id,
                'payee_id' => $helpmate->id,
                'amount' => $taskData['budget'],
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(1),
            ]);
        }

        $this->command->info('✓ Seeded ' . (count($tasks) + count($pendingTasks)) . ' payments');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Payment;
use App\Models\TrustedContact;
use App\Models\User;

class FeatureDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Resource Categories
        $categories = [
            ['name' => 'Accessibility Guides', 'description' => 'Guides for accessibility'],
            ['name' => 'Health & Wellness', 'description' => 'Health and wellness resources'],
            ['name' => 'Legal & Financial', 'description' => 'Legal and financial information'],
            ['name' => 'Technology', 'description' => 'Technology guides and tools'],
            ['name' => 'Community Support', 'description' => 'Community support resources'],
        ];

        foreach ($categories as $category) {
            ResourceCategory::firstOrCreate(['name' => $category['name']], $category);
        }

        // Create Sample Resources
        $resources = [
            [
                'title' => 'Complete Guide to Home Accessibility',
                'description' => 'A comprehensive guide to making your home more accessible with practical tips and modifications.',
                'type' => 'pdf',
                'category' => 'Accessibility Guides',
                'url' => 'https://example.com/accessibility-guide.pdf',
            ],
            [
                'title' => 'Mental Health Resources for Seniors',
                'description' => 'Essential mental health resources and support services for older adults.',
                'type' => 'video',
                'category' => 'Health & Wellness',
                'url' => 'https://example.com/mental-health-video',
            ],
            [
                'title' => 'Understanding Disability Benefits',
                'description' => 'A detailed guide to understanding and applying for disability benefits.',
                'type' => 'pdf',
                'category' => 'Legal & Financial',
                'url' => 'https://example.com/disability-benefits.pdf',
            ],
            [
                'title' => 'Assistive Technology Basics',
                'description' => 'Introduction to assistive technologies that can improve daily living.',
                'type' => 'video',
                'category' => 'Technology',
                'url' => 'https://example.com/assistive-tech-video',
            ],
            [
                'title' => 'Local Support Groups Directory',
                'description' => 'Find local support groups and community resources in your area.',
                'type' => 'pdf',
                'category' => 'Community Support',
                'url' => 'https://example.com/support-groups.pdf',
            ],
            [
                'title' => 'Nutrition Guide for PWDs',
                'description' => 'Nutritional guidelines and meal planning for persons with disabilities.',
                'type' => 'pdf',
                'category' => 'Health & Wellness',
                'url' => 'https://example.com/nutrition.pdf',
            ],
        ];

        foreach ($resources as $resource) {
            Resource::firstOrCreate(
                ['title' => $resource['title']],
                $resource
            );
        }

        // Create sample trusted contacts for test user
        $user = User::where('email', 'user@example.com')->first();
        if ($user) {
            $contacts = [
                [
                    'user_id' => $user->id,
                    'contact_name' => 'Dr. Sarah Johnson',
                    'contact_phone' => '+1 555-0101',
                    'contact_email' => 'sarah.johnson@hospital.com',
                    'relation' => 'Healthcare Provider',
                ],
                [
                    'user_id' => $user->id,
                    'contact_name' => 'John Smith',
                    'contact_phone' => '+1 555-0102',
                    'contact_email' => 'john.smith@email.com',
                    'relation' => 'Family',
                ],
                [
                    'user_id' => $user->id,
                    'contact_name' => 'Emergency Services',
                    'contact_phone' => '911',
                    'contact_email' => null,
                    'relation' => 'Other',
                ],
            ];

            foreach ($contacts as $contact) {
                TrustedContact::firstOrCreate(
                    ['user_id' => $contact['user_id'], 'contact_name' => $contact['contact_name']],
                    $contact
                );
            }
        }

        // Create sample payment records
        $helpmate = User::where('email', 'helpmate@example.com')->first();
        if ($user && $helpmate) {
            // Create a sample completed task for payment history
            $completedTask = \App\Models\Task::create([
                'created_by' => $user->id,
                'caregiver_id' => $helpmate->id,
                'title' => 'Grocery Shopping',
                'description' => 'Help with weekly grocery shopping',
                'location' => 'Local Supermarket',
                'budget' => 50.00,
                'status' => 'completed',
                'urgency' => 'low',
                'scheduled_at' => now()->subDays(7),
            ]);

            $payments = [
                [
                    'payer_id' => $user->id,
                    'payee_id' => $helpmate->id,
                    'task_id' => $completedTask->id,
                    'amount' => 50.00,
                    'status' => 'paid',
                    'paid_at' => now()->subDays(6),
                ],
                [
                    'payer_id' => $user->id,
                    'payee_id' => $helpmate->id,
                    'task_id' => $completedTask->id,
                    'amount' => 75.50,
                    'status' => 'paid',
                    'paid_at' => now()->subDays(3),
                ],
                [
                    'payer_id' => $user->id,
                    'payee_id' => $helpmate->id,
                    'task_id' => $completedTask->id,
                    'amount' => 100.00,
                    'status' => 'pending',
                ],
            ];

            foreach ($payments as $payment) {
                Payment::create($payment);
            }
        }

        $this->command->info('Feature data seeded successfully!');
    }
}

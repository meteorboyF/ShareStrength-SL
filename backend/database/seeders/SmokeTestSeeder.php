<?php

namespace Database\Seeders;

use App\Models\AccessibilitySetting;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Community;
use App\Models\CommunityComment;
use App\Models\Conversation;
use App\Models\Helper;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Task;
use App\Models\TrustedContact;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SmokeTestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@sharestrength.test'],
            [
                'name' => 'Admin Tester',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $pwd = User::firstOrCreate(
            ['email' => 'pwd@sharestrength.test'],
            [
                'name' => 'Pat PWD',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
                'phone' => '555-0101',
                'address' => '100 Access Lane',
                'location' => 'Springfield',
                'disability_type' => 'Mobility',
                'bio' => 'PWD test account.',
                'is_active' => true,
            ]
        );

        $helpmate = Helper::firstOrCreate(
            ['email' => 'helpmate@sharestrength.test'],
            [
                'name' => 'Casey HelpMate',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
                'phone' => '555-0102',
                'address' => '200 Care Way',
                'location' => 'Springfield',
                'skills' => 'Mobility Support, Cooking',
                'bio' => 'HelpMate test account.',
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        AccessibilitySetting::updateOrCreate(
            ['user_id' => $pwd->id],
            ['font_size' => 'medium', 'tts_enabled' => false, 'stt_enabled' => false, 'high_contrast' => false]
        );

        $category = ResourceCategory::firstOrCreate(
            ['name' => 'Accessibility Guides'],
            ['description' => 'Guides and references', 'icon' => 'book']
        );

        Resource::firstOrCreate(
            ['title' => 'Home Accessibility Basics'],
            [
                'description' => 'Quick tips for making a home more accessible.',
                'type' => 'accessible_pdf',
                'category_id' => $category->id,
                'file_url' => 'https://example.com/resources/accessibility-basics.pdf',
                'language' => 'English',
                'author' => 'ShareStrength',
                'uploaded_by' => $admin->id,
                'is_featured' => true,
                'download_count' => 0,
            ]
        );

        $product = Product::firstOrCreate(
            ['name' => 'Grip Assist Tool'],
            [
                'description' => 'Assistive grip tool for daily tasks.',
                'price' => 19.99,
                'image_url' => 'https://placehold.co/600x400',
                'stock_quantity' => 50,
                'category' => 'Assistive Devices',
                'vendor' => 'ShareStrength',
            ]
        );

        $order = Order::firstOrCreate(
            ['user_id' => $pwd->id, 'status' => 'paid'],
            [
                'total_amount' => 19.99,
                'payment_details' => ['method' => 'seed'],
                'shipping_address' => ['address' => '100 Access Lane'],
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order->id, 'product_id' => $product->id],
            ['quantity' => 1, 'price' => 19.99]
        );

        $openTask = Task::firstOrCreate(
            ['title' => 'Grocery Pickup Assistance', 'created_by' => $pwd->id],
            [
                'description' => 'Need help picking up groceries.',
                'location' => 'Springfield',
                'budget' => 30.00,
                'status' => 'open',
                'required_skills' => ['Transport'],
                'urgency' => 'medium',
                'scheduled_at' => now()->addDay(),
            ]
        );

        Application::firstOrCreate(
            ['task_id' => $openTask->id, 'helper_id' => $helpmate->id],
            ['applicant_type' => 'helper', 'status' => 'pending']
        );

        $completedTask = Task::firstOrCreate(
            ['title' => 'Medication Pickup', 'created_by' => $pwd->id, 'status' => 'completed'],
            [
                'description' => 'Pick up medication from pharmacy.',
                'location' => 'Springfield',
                'budget' => 25.00,
                'status' => 'completed',
                'required_skills' => ['Transport'],
                'urgency' => 'low',
                'caregiver_id' => $helpmate->id,
                'started_at' => Carbon::now()->subHours(2),
                'completed_at' => Carbon::now()->subHour(),
            ]
        );

        Payment::firstOrCreate(
            ['task_id' => $completedTask->id],
            [
                'payer_id' => $pwd->id,
                'payee_id' => $helpmate->id,
                'amount' => 25.00,
                'status' => 'paid',
                'paid_at' => now()->subMinutes(30),
            ]
        );

        $conversation = Conversation::findOrCreate(
            $pwd->id,
            'user',
            $helpmate->id,
            'helper',
            $openTask->id
        );

        Message::firstOrCreate(
            ['conversation_id' => $conversation->id, 'content' => 'Hi! Are you available tomorrow?'],
            [
                'task_id' => $openTask->id,
                'sender_id' => $pwd->id,
                'sender_type' => 'user',
                'receiver_id' => $helpmate->id,
                'receiver_type' => 'helper',
                'is_read' => false,
            ]
        );

        $post = Community::firstOrCreate(
            ['user_id' => $pwd->id, 'content' => 'Looking for accessibility tips.'],
            ['media_url' => null, 'status' => 'active']
        );

        CommunityComment::firstOrCreate(
            ['community_id' => $post->id, 'user_id' => $pwd->id],
            ['comment' => 'Happy to connect with others here.']
        );

        TrustedContact::firstOrCreate(
            ['user_id' => $pwd->id, 'contact_name' => 'Jordan Smith'],
            [
                'relation' => 'Friend',
                'contact_email' => 'jordan@example.com',
                'contact_phone' => '555-0199',
                'status' => 'verified',
            ]
        );
    }
}

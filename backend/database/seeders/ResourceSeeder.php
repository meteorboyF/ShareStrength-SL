<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Admin;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old resources to prevent duplicates
        Resource::truncate();

        // Find or create a category to use
        $guideCategory = ResourceCategory::firstOrCreate(
            ['name' => 'Accessibility Guides'],
            ['description' => 'Guides and references', 'icon' => 'book']
        );

        $techCategory = ResourceCategory::firstOrCreate(
            ['name' => 'Tech How-Tos'],
            ['description' => 'Tutorials for assistive technology.', 'icon' => 'computer']
        );

        // Get the first admin user to assign as the uploader
        $adminUploader = Admin::first();
        $adminId = $adminUploader ? $adminUploader->id : 1;

        Resource::create([
            'title' => 'Home Accessibility Basics',
            'type' => 'accessible_pdf',
            'category_id' => $guideCategory->id,
            'file_url' => 'resources/sample.pdf',
            'file_path' => 'resources/sample.pdf', // <-- ADDED THIS
            'is_featured' => true,
            'description' => 'A comprehensive guide to making your home more accessible for individuals with mobility challenges.',
            'author' => 'ShareStrength Org',
            'uploaded_by' => $adminId,
        ]);

        Resource::create([
            'title' => 'Intro to Screen Readers',
            'type' => 'audiobook',
            'category_id' => $techCategory->id,
            'file_url' => 'resources/sample_audio.mp3',
            'file_path' => 'resources/sample_audio.mp3', // <-- ADDED THIS
            'is_featured' => false,
            'description' => 'An audiobook explaining the fundamentals of using screen reader software like JAWS and NVDA.',
            'author' => 'Tech-Ease',
            'narrator' => 'Jane Doe',
            'duration' => 900,
            'uploaded_by' => $adminId,
        ]);

        Resource::create([
            'title' => 'Basic Sign Language Video',
            'type' => 'sign_language_video',
            'category_id' => $guideCategory->id,
            'file_url' => 'resources/sample_video.mp4',
            'file_path' => 'resources/sample_video.mp4', // <-- ADDED THIS
            'is_featured' => false,
            'description' => 'A short video tutorial covering the alphabet and common phrases in ASL.',
            'author' => 'Signers United',
            'uploaded_by' => $adminId,
        ]);
    }
}
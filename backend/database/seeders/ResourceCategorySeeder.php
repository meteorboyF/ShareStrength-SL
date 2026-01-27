<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResourceCategory;

class ResourceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fiction',
                'description' => 'Fictional stories, novels, and literature',
                'icon' => '📚'
            ],
            [
                'name' => 'Non-Fiction',
                'description' => 'Factual books, biographies, and documentaries',
                'icon' => '📖'
            ],
            [
                'name' => 'Education',
                'description' => 'Educational materials, textbooks, and tutorials',
                'icon' => '🎓'
            ],
            [
                'name' => 'News & Current Affairs',
                'description' => 'News articles, current events, and journalism',
                'icon' => '📰'
            ],
            [
                'name' => 'Self-Help',
                'description' => 'Personal development and self-improvement',
                'icon' => '💪'
            ],
            [
                'name' => 'Children\'s Books',
                'description' => 'Books and materials for children',
                'icon' => '👶'
            ],
            [
                'name' => 'Religious',
                'description' => 'Religious texts and spiritual materials',
                'icon' => '🕌'
            ],
            [
                'name' => 'Technology',
                'description' => 'Tech guides, programming, and digital literacy',
                'icon' => '💻'
            ],
        ];

        foreach ($categories as $category) {
            ResourceCategory::create($category);
        }
    }
}

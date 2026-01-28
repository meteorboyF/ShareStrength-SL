<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        return [
            'uploaded_by' => Admin::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'file_path' => 'resources/sample.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'is_public' => true,
            'category_id' => ResourceCategory::factory(),
            'type' => 'pdf',
        ];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => "Ergonomic Wheelchair",
                'description' => "Comfortable and durable wheelchair designed for daily use.",
                'vendor' => "MobilityPlus",
                'price' => 2499.99,
                'category' => "Mobility",
                'stock_quantity' => 10,
                'image_url' => "/products/wheelchair.png"
            ],
            [
                'name' => "Braille Smartwatch",
                'description' => "Smartwatch with braille display interface for accessibility.",
                'vendor' => "TechVision",
                'price' => 299.50,
                'category' => "Vision",
                'stock_quantity' => 3,
                'image_url' => "/products/watch.jpg"
            ],
            [
                'name' => "Hearing Aid Pro",
                'description' => "High-fidelity digital hearing aid with noise cancellation.",
                'vendor' => "AudioLife",
                'price' => 899.00,
                'category' => "Hearing",
                'stock_quantity' => 15,
                'image_url' => "/products/hearing-aid.jpg"
            ],
            [
                'name' => "Smart Walking Stick",
                'description' => "Walking stick with sensors and obstacle detection.",
                'vendor' => "MobilityPlus",
                'price' => 120.00,
                'category' => "Mobility",
                'stock_quantity' => 8,
                'image_url' => "/products/stick.jpg"
            ],
            [
                'name' => "Voice-To-Text Tablet",
                'description' => "Tablet optimized for voice-to-text communication.",
                'vendor' => "TechVision",
                'price' => 450.00,
                'category' => "Communication",
                'stock_quantity' => 5,
                'image_url' => "/products/tablet.jpg"
            ],
            [
                'name' => "Adaptive Kitchen Kit",
                'description' => "Set of ergonomic kitchen tools including specialized knives and easy-grip utensils.",
                'vendor' => "HomeAid",
                'price' => 85.00,
                'category' => "Daily Living",
                'stock_quantity' => 20,
                'image_url' => "/products/kitchen-kit.jpg"
            ],
            [
                'name' => "Digital Magnifier",
                'description' => "Portable electronic magnifier with high contrast modes for reading.",
                'vendor' => "VisionAid",
                'price' => 150.00,
                'category' => "Vision",
                'stock_quantity' => 12,
                'image_url' => "/products/magnifier.jpg"
            ],
            [
                'name' => "Shower Chair",
                'description' => "Non-slip adjustable shower chair with backrest for safety.",
                'vendor' => "MobilityPlus",
                'price' => 45.00,
                'category' => "Daily Living",
                'stock_quantity' => 25,
                'image_url' => "/products/shower-chair.jpg"
            ]
        ];

        foreach ($products as $product) {
            \App\Models\Product::updateOrCreate(
                ['name' => $product['name']], // Check by name
                $product
            );
        }
    }
}

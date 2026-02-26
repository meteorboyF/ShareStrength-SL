<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Disable Foreign Key Checks (so we can delete products even if they are in orders)
        Schema::disableForeignKeyConstraints();

        // 2. Wipe the table clean!
        Product::truncate();

        // 3. Re-enable Foreign Key Checks
        Schema::enableForeignKeyConstraints();

        $products = [
            [
                'name' => 'Eye-Tracking Smart Hub',
                'description' => 'Control your smart home and computer using only your eyes. Perfect for individuals with limited mobility.',
                'price' => 450.00,
                'vendor' => 'VisionTech',
                'category' => 'tech',
                'image_url' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 12,
            ],
            [
                'name' => 'Braille Display Pro',
                'description' => 'Refreshable braille display with 40 cells and seamless Bluetooth connectivity.',
                'price' => 2100.00,
                'vendor' => 'DotSystems',
                'category' => 'vision',
                'image_url' => 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 2,
            ],
            [
                'name' => 'Voice Door Opener',
                'description' => 'Voice-activated door opener system compatible with Alexa and Google Home.',
                'price' => 180.00,
                'vendor' => 'HomeSense',
                'category' => 'smart home',
                'image_url' => 'https://images.unsplash.com/photo-1558002038-1055907df827?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Adaptive Gaming Mouse',
                'description' => 'Fully customizable gaming controller with large buttons and switch inputs.',
                'price' => 95.00,
                'vendor' => 'LogiCare',
                'category' => 'tech',
                'image_url' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 20,
            ],
            [
                'name' => 'Haptic Alarm Clock',
                'description' => 'Vibrating alarm clock with bed shaker attachment for deep sleepers or hard of hearing.',
                'price' => 65.00,
                'vendor' => 'SilentAlert',
                'category' => 'hearing',
                'image_url' => 'https://images.unsplash.com/photo-1563991655280-cb95c90ca2fb?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 5,
            ],
            [
                'name' => 'AI Text-to-Speech Glasses',
                'description' => 'Wearable glasses that read text aloud from books, signs, and screens instantly.',
                'price' => 3200.00,
                'vendor' => 'OrCam',
                'category' => 'vision',
                'image_url' => 'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 4,
            ],
            [
                'name' => 'All-Terrain Powerchair',
                'description' => 'Heavy-duty electric wheelchair designed for outdoor terrain and stability.',
                'price' => 4800.00,
                'vendor' => 'Apex Mobility',
                'category' => 'mobility',
                'image_url' => 'https://images.unsplash.com/photo-1534346589587-9b51347279eb?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 1,
            ],
            [
                'name' => 'Noise Cancelling Headphones',
                'description' => 'Premium noise cancelling headphones to help with sensory overload.',
                'price' => 250.00,
                'vendor' => 'Sony',
                'category' => 'hearing',
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=800&auto=format&fit=crop',
                'stock_quantity' => 10,
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
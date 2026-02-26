<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Marketplace extends Component
{
    public $search = '';
    public $category = 'all';
    public $categories = [];

    public function mount()
    {
        // Dynamically fetch unique categories from the database
        // If DB is empty, it falls back to a default list so the UI isn't broken
        $dbCategories = Product::distinct()->whereNotNull('category')->pluck('category')->toArray();
        
        $this->categories = !empty($dbCategories) 
            ? $dbCategories 
            : ['mobility', 'tech', 'vision', 'hearing', 'smart home'];
    }

    #[Layout('components.layouts.app', ['title' => 'Marketplace - ShareStrength'])]
    public function render()
    {
        // Start the query
        $query = Product::query();

        // 1. Apply Search Filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Apply Category Filter
        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }

        // 3. Get Results (Newest first)
        $products = $query->latest()->get();

        return view('livewire.shop.marketplace', [
            'products' => $products,
            'isHelpmate' => Auth::guard('helpmate')->check(),
        ]);
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

public function addToCart($productId)
    {
        // Find the REAL product from the database
        $product = Product::find($productId);

        if (!$product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        $cart = session()->get('cart', []);
        $imageUrl = $this->getImageUrl($product); // Get the correct URL

        // Logic: Add to cart or increment quantity
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image_url' => $imageUrl,
                'vendor' => $product->vendor ?? 'ShareStrength',
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated'); 
        
        // --- UPDATED THIS SECTION ---
        // Instead of a simple string, we send an array with details
        session()->flash('added_to_cart', [
            'name' => $product->name,
            'image' => $imageUrl,
            'price' => $product->price
        ]);
    }

    /**
     * Helper to resolve image URL
     */
    private function getImageUrl($product)
    {
        if (empty($product->image_url)) {
            return 'https://placehold.co/600x400?text=No+Image';
        }

        if (str_starts_with($product->image_url, 'http')) {
            return $product->image_url;
        }

        return asset('storage/' . $product->image_url);
    }
}
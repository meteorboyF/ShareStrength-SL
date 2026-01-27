<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class Marketplace extends Component
{
    public $search = '';
    public $category = 'all';
    public $categories = [];

    public function mount()
    {
        $this->categories = Product::distinct()->pluck('category')->filter()->toArray();
    }

    #[Layout('components.layouts.app', ['title' => 'Marketplace - ShareStrength'])]
    public function render()
    {
        $products = Product::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category !== 'all', function ($query) {
                $query->where('category', $this->category);
            })
            ->latest()
            ->get();

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
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $product = Product::find($productId);
            if ($product) {
                $cart[$productId] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image_url' => $product->image_url,
                    'vendor' => $product->vendor,
                    'quantity' => 1,
                ];
            }
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
        session()->flash('success', 'Added to cart!');
    }
}

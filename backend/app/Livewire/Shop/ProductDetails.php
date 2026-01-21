<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

class ProductDetails extends Component
{
    public $product;
    public $quantity = 1;

    public function mount($id)
    {
        $this->product = Product::findOrFail($id);
    }

    #[Layout('components.layouts.app', ['title' => 'Product Details - ShareStrength'])]
    public function render()
    {
        return view('livewire.shop.product-details');
    }

    public function increment()
    {
        if ($this->quantity < $this->product->stock_quantity) {
            $this->quantity++;
        }
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $cart = session()->get('cart', []);
        $productId = $this->product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $this->quantity;
        } else {
            $cart[$productId] = [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'image_url' => $this->product->image_url,
                'vendor' => $this->product->vendor,
                'quantity' => $this->quantity,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
        session()->flash('success', 'Added ' . $this->quantity . ' item(s) to cart!');
    }
}

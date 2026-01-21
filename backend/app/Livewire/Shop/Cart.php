<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    #[Layout('components.layouts.app', ['title' => 'Shopping Cart - ShareStrength'])]
    public function render()
    {
        $cartTotal = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return view('livewire.shop.cart', [
            'cartTotal' => $cartTotal,
        ]);
    }

    public function updateQuantity($productId, $change)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = max(1, $cart[$productId]['quantity'] + $change);
            session()->put('cart', $cart);
            $this->cart = $cart;
        }
    }

    public function removeItem($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        $this->cart = $cart;

        session()->flash('success', 'Item removed from cart.');
    }

    public function clearCart()
    {
        session()->forget('cart');
        $this->cart = [];
        session()->flash('success', 'Cart cleared.');
    }
}

<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;

class Checkout extends Component
{
    public $cart = [];
    public $cartTotal = 0;

    // Form fields
    public $cardholderName = '';
    public $cardNumber = '';
    public $expiry = '';
    public $cvc = '';
    public $shippingAddress = '';

    public $processing = false;

    protected $rules = [
        'cardholderName' => 'required|string|min:2',
        'cardNumber' => 'required|string|min:16',
        'expiry' => 'required|string',
        'cvc' => 'required|string|min:3',
        'shippingAddress' => 'required|string|min:10',
    ];

    public function mount()
    {
        $this->cart = session()->get('cart', []);

        if (empty($this->cart)) {
            return redirect()->route('marketplace');
        }

        $this->cartTotal = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    #[Layout('components.layouts.app', ['title' => 'Checkout - ShareStrength'])]
    public function render()
    {
        return view('livewire.shop.checkout');
    }

    public function processPayment()
    {
        $this->validate();
        $this->processing = true;

        try {
            // Create order
            $order = Order::create([
                'user_id' => Auth::guard('pwd')->id(),
                'total_amount' => $this->cartTotal,
                'status' => 'pending',
                'shipping_address' => ['address' => $this->shippingAddress],
                'payment_details' => ['method' => 'card', 'name' => $this->cardholderName],
            ]);

            // Create order items
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Update order status to paid (simulated)
            $order->update(['status' => 'paid']);

            // Clear cart
            session()->forget('cart');

            session()->flash('success', 'Payment successful! Your order has been placed.');
            return $this->redirect(route('dashboard'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Payment failed: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }
}

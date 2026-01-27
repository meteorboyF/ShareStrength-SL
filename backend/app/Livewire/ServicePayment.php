<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Task;

class ServicePayment extends Component
{
    public $payment;
    public $task;
    public $helper;
    public $processing = false;

    public $subtotal = 0;
    public $platformFee = 0;
    public $total = 0;

    public function mount($paymentId)
    {
        $this->payment = Payment::with(['task', 'payee'])->findOrFail($paymentId);
        if (!Auth::guard('pwd')->check() || $this->payment->payer_id !== Auth::guard('pwd')->id()) {
            abort(403);
        }
        $this->task = $this->payment->task;
        $this->helper = $this->payment->payee;

        $this->subtotal = (float)$this->payment->amount;
        $this->platformFee = $this->subtotal * 0.10; // 10% platform fee
        $this->total = $this->subtotal + $this->platformFee;
    }

    #[Layout('components.layouts.app', ['title' => 'Service Payment - ShareStrength'])]
    public function render()
    {
        return view('livewire.service-payment');
    }

    public function processPayment()
    {
        $this->processing = true;

        try {
            // Update payment status to paid
            $this->payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            session()->flash('success', "Payment of \${$this->total} to {$this->helper->name} successful!");
            return $this->redirect(route('dashboard'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Payment failed: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }
}

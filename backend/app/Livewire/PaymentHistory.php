<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentHistory extends Component
{
    public $filterType = 'all'; // all, sent, received

    #[Layout('components.layouts.app', ['title' => 'Payment History - ShareStrength'])]
    public function render()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Get payments based on filter
        $paymentsQuery = Payment::where(function ($query) use ($user) {
            $query->where('payer_id', $user->id)
                ->orWhere('payee_id', $user->id);
        })->with(['payer', 'payee', 'task']);

        if ($this->filterType === 'sent') {
            $paymentsQuery->where('payer_id', $user->id);
        } elseif ($this->filterType === 'received') {
            $paymentsQuery->where('payee_id', $user->id);
        }

        $payments = $paymentsQuery->latest()->get();

        // Calculate summary
        $totalSent = Payment::where('payer_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $totalReceived = Payment::where('payee_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $pendingPayments = Payment::where(function ($query) use ($user) {
            $query->where('payer_id', $user->id)
                ->orWhere('payee_id', $user->id);
        })->where('status', 'pending')->count();

        // Analysis Data (For 'Sent' perspective mainly)
        $monthlySpending = Payment::where('payer_id', $user->id)
            ->where('status', 'paid')
            ->selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, SUM(amount) as total') // Using created_at or paid_at if available
            ->groupBy('month')
            ->orderByRaw('MIN(created_at) ASC')
            ->limit(6)
            ->get();

        $spendingByHelper = Payment::where('payer_id', $user->id)
            ->where('status', 'paid')
            ->with('payee:id,name')
            ->selectRaw('payee_id, SUM(amount) as total')
            ->groupBy('payee_id')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();

        return view('livewire.payment-history', [
            'payments' => $payments,
            'totalSent' => $totalSent,
            'totalReceived' => $totalReceived,
            'pendingPayments' => $pendingPayments,
            'monthlySpending' => $monthlySpending,
            'spendingByHelper' => $spendingByHelper,
        ]);
    }

    public function setFilter($type)
    {
        $this->filterType = $type;
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}

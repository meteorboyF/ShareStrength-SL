<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentHistory extends Component
{
    public $filterType = 'all'; // all, sent

    #[Layout('components.layouts.app', ['title' => 'Payment History - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('pwd')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('pwd')->user();

        // Get payments based on filter
        $paymentsQuery = Payment::where('payer_id', $user->id)
            ->with(['payer', 'payee', 'task']);

        if ($this->filterType === 'sent') {
            $paymentsQuery->where('payer_id', $user->id);
        }

        $payments = $paymentsQuery->latest()->get();

        // Calculate summary
        $totalSent = Payment::where('payer_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $pendingPayments = Payment::where('payer_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Analysis Data (For 'Sent' perspective mainly)
        $monthlySpending = Payment::where('payer_id', $user->id)
            ->where('status', 'paid')
            ->orderBy('paid_at')
            ->get()
            ->groupBy(function ($payment) {
                $date = $payment->paid_at ?? $payment->created_at;
                return $date->format('Y-m');
            })
            ->map(function ($group) {
                $first = $group->first();
                $date = $first->paid_at ?? $first->created_at;
                return (object) [
                    'month' => $date->format('M Y'),
                    'total' => (float) $group->sum('amount'),
                ];
            })
            ->sortKeys()
            ->values();

        if ($monthlySpending->count() > 6) {
            $monthlySpending = $monthlySpending->slice(-6)->values();
        }

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
        Auth::guard('pwd')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}

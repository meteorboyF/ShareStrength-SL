<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentInsights extends Component
{
    public $monthlyData = [];
    public $helperData = [];
    public $taskBreakdown = [];

    public function mount()
    {
        $userId = Auth::guard('pwd')->id();

        // Get monthly spending data
        $payments = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->orderBy('paid_at')
            ->get();

        $monthlyTotals = $payments
            ->groupBy(function ($payment) {
                $date = $payment->paid_at ?? $payment->created_at;
                return $date->format('Y-m');
            })
            ->map(function ($group) {
                $first = $group->first();
                $date = $first->paid_at ?? $first->created_at;
                return [
                    'label' => $date->format('M Y'),
                    'total' => $group->sum('amount'),
                ];
            })
            ->sortKeys()
            ->values();

        if ($monthlyTotals->count() > 6) {
            $monthlyTotals = $monthlyTotals->slice(-6)->values();
        }

        $this->monthlyData = [
            'labels' => $monthlyTotals->pluck('label')->toArray(),
            'values' => $monthlyTotals->pluck('total')->map(fn($v) => (float)$v)->toArray(),
        ];

        // If no data, use sample data
        if (empty($this->monthlyData['labels'])) {
            $this->monthlyData = [
                'labels' => ['Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025'],
                'values' => [450, 320, 500, 150],
            ];
        }

        // Get spending by helper
        $helperPayments = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->with('payee')
            ->selectRaw('payee_id, SUM(amount) as total')
            ->groupBy('payee_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $this->helperData = $helperPayments->map(function ($p) {
            return [
                'name' => $p->payee->name ?? 'Unknown',
                'amount' => (float)$p->total,
            ];
        })->toArray();

        // If no data, use sample data
        if (empty($this->helperData)) {
            $this->helperData = [
                ['name' => 'Sarah Smith', 'amount' => 800],
                ['name' => 'Mike Ross', 'amount' => 450],
                ['name' => 'John Doe', 'amount' => 170],
            ];
        }

        // Get task breakdown
        $taskPayments = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->with(['task', 'payee'])
            ->latest()
            ->limit(10)
            ->get();

        $this->taskBreakdown = $taskPayments->map(function ($p) {
            return [
                'title' => $p->task->title ?? 'Unknown Task',
                'helper' => $p->payee->name ?? 'Unknown',
                'fee' => (float)$p->amount,
            ];
        })->toArray();

        // If no data, use sample data
        if (empty($this->taskBreakdown)) {
            $this->taskBreakdown = [
                ['title' => 'Math Tutoring', 'helper' => 'Sarah Smith', 'fee' => 150.00],
                ['title' => 'Grocery Shopping', 'helper' => 'Mike Ross', 'fee' => 55.50],
                ['title' => 'Garden Cleaning', 'helper' => 'John Doe', 'fee' => 40.00],
            ];
        }
    }

    #[Layout('components.layouts.app', ['title' => 'Payment Insights - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('pwd')->check()) {
            return redirect()->route('login');
        }

        return view('livewire.payment-insights');
    }
}

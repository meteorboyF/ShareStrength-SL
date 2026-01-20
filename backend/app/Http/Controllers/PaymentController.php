<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Return payments where user is payer or payee
        return Payment::where('payer_id', $request->user()->id)
            ->orWhere('payee_id', $request->user()->id)
            ->with(['task:id,title', 'payer:id,name', 'payee:id,name'])
            ->latest()
            ->get();
    }

    /**
     * Get payment summary for the authenticated user
     */
    public function summary(Request $request)
    {
        $userId = $request->user()->id;

        // Get all payments where user is the payer
        $payments = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->get();

        $totalSpent = $payments->sum('amount');
        $platformFeeRate = 0.10; // 10% platform fee
        $platformFees = $totalSpent * $platformFeeRate;
        $toHelpers = $totalSpent - $platformFees;

        return response()->json([
            'total_spent' => (float) $totalSpent,
            'to_helpers' => (float) $toHelpers,
            'platform_fees' => (float) $platformFees,
            'transaction_count' => $payments->count(),
        ]);
    }

    /**
     * Get detailed payment insights
     */
    public function insights(Request $request)
    {
        $userId = $request->user()->id;

        // Monthly spending breakdown
        $monthlyData = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->selectRaw('DATE_FORMAT(paid_at, "%b %Y") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderByRaw('MIN(paid_at) ASC')
            ->limit(12)
            ->get();

        // Spending by helper
        $helperData = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->with('payee:id,name')
            ->selectRaw('payee_id, SUM(amount) as total')
            ->groupBy('payee_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'name' => $payment->payee->name ?? 'Unknown',
                    'amount' => (float) $payment->total,
                ];
            });

        // Spending by task
        $taskData = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->with(['task:id,title', 'payee:id,name'])
            ->orderBy('paid_at', 'DESC')
            ->limit(20)
            ->get()
            ->map(function ($payment) {
                return [
                    'title' => $payment->task->title ?? 'Unknown Task',
                    'helper' => $payment->payee->name ?? 'Unknown',
                    'fee' => (float) $payment->amount,
                    'date' => $payment->paid_at->format('Y-m-d'),
                ];
            });

        return response()->json([
            'monthly' => [
                'labels' => $monthlyData->pluck('month'),
                'values' => $monthlyData->pluck('total')->map(fn($v) => (float) $v),
            ],
            'helpers' => $helperData,
            'tasks' => $taskData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'payee_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create([
            ...$validated,
            'payer_id' => $request->user()->id,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return response()->json($payment, 201);
    }
}

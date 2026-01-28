<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Helper;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user instanceof Helper) {
            return Payment::where('payee_id', $user->getKey())
                ->with(['task:id,title', 'payer:id,name', 'payee:id,name'])
                ->latest()
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'task_id' => $payment->task_id,
                        'payer_id' => $payment->payer_id,
                        'payee_id' => $payment->payee_id,
                        'amount' => $payment->amount,
                        'hours_worked' => $payment->hours_worked,
                        'hourly_rate' => $payment->hourly_rate,
                        'status' => $payment->status,
                        'paid_at' => $payment->paid_at,
                        'task' => $payment->task,
                        'payee' => $payment->payee,
                        'payer' => $payment->payer,
                        'created_at' => $payment->created_at,
                        'updated_at' => $payment->updated_at
                    ];
                });
        }

        if ($user instanceof User) {
            return Payment::where('payer_id', $user->getKey())
                ->with(['task:id,title', 'payer:id,name', 'payee:id,name'])
                ->latest()
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'task_id' => $payment->task_id,
                        'payer_id' => $payment->payer_id,
                        'payee_id' => $payment->payee_id,
                        'amount' => $payment->amount,
                        'hours_worked' => $payment->hours_worked,
                        'hourly_rate' => $payment->hourly_rate,
                        'status' => $payment->status,
                        'paid_at' => $payment->paid_at,
                        'task' => $payment->task,
                        'payee' => $payment->payee,
                        'payer' => $payment->payer,
                        'created_at' => $payment->created_at,
                        'updated_at' => $payment->updated_at
                    ];
                });
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Get payment summary for the authenticated user
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can view summaries'], 403);
        }
        $userId = $user->getKey();

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
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can view insights'], 403);
        }
        $userId = $user->getKey();

        // Monthly spending breakdown
        $monthlyPayments = Payment::where('payer_id', $userId)
            ->where('status', 'paid')
            ->orderBy('paid_at')
            ->get();

        $monthlyBuckets = $monthlyPayments
            ->groupBy(function ($payment) {
                $date = $payment->paid_at ?? $payment->created_at;
                return $date->format('Y-m');
            })
            ->map(function ($group) {
                $first = $group->first();
                $date = $first->paid_at ?? $first->created_at;
                return [
                    'label' => $date->format('M Y'),
                    'total' => (float) $group->sum('amount'),
                ];
            })
            ->sortKeys()
            ->values();

        if ($monthlyBuckets->count() > 12) {
            $monthlyBuckets = $monthlyBuckets->slice(-12)->values();
        }

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
                'labels' => $monthlyBuckets->pluck('label'),
                'values' => $monthlyBuckets->pluck('total')->map(fn($v) => (float) $v),
            ],
            'helpers' => $helperData,
            'tasks' => $taskData,
            'spending_summary' => [
                'last_1_month' => (float) Payment::where('payer_id', $userId)->where('status', 'paid')->where('paid_at', '>=', now()->subMonth())->sum('amount'),
                'last_6_months' => (float) Payment::where('payer_id', $userId)->where('status', 'paid')->where('paid_at', '>=', now()->subMonths(6))->sum('amount'),
                'last_1_year' => (float) Payment::where('payer_id', $userId)->where('status', 'paid')->where('paid_at', '>=', now()->subYear())->sum('amount'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can create payments'], 403);
        }

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'payee_id' => 'required|exists:helpers,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create([
            ...$validated,
            'payer_id' => $user->getKey(),
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json($payment, 201);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $user = $request->user();

        // Authorization
        if ($payment->payer_id !== $user->getKey()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:paid',
        ]);

        if ($validated['status'] === 'paid' && $payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            // Start a conversation or send notification if needed
        }

        return response()->json($payment);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    /**
     * Store a new donation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'donor_name' => 'nullable|string|max:255',
            'is_monthly' => 'boolean',
            'status' => 'nullable|string|in:pending,completed,failed',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $donation = Donation::create($validated);

        return response()->json([
            'message' => 'Thank you for your donation!',
            'donation' => $donation
        ], 201);
    }

    /**
     * Get financial transparency data.
     */
    public function index()
    {
        // Calculate totals
        $totalRaised = Donation::sum('amount');
        $totalSpent = Expense::sum('amount');
        $remainingFunds = $totalRaised - $totalSpent;
        $fundsUsedPercentage = $totalRaised > 0 ? round(($totalSpent / $totalRaised) * 100, 1) : 0;

        // Get monthly data for the chart (current year)
        $monthlyRaised = Donation::select(DB::raw('SUM(amount) as total'), DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyExpenses = Expense::select(DB::raw('SUM(amount) as total'), DB::raw('MONTH(date) as month'))
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Normalize monthly data for chart (1-12)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = [
            'labels' => $months,
            'raised' => [],
            'expenses' => []
        ];

        for ($i = 1; $i <= 12; $i++) {
            $chartData['raised'][] = $monthlyRaised->get($i, 0);
            $chartData['expenses'][] = $monthlyExpenses->get($i, 0);
        }

        // Get recent transactions
        $recentExpenses = Expense::latest('date')->take(5)->get();

        return response()->json([
            'total_raised' => $totalRaised,
            'funds_used_percentage' => $fundsUsedPercentage,
            'remaining_funds' => $remainingFunds,
            'chart_data' => $chartData,
            'recent_expenses' => $recentExpenses
        ]);
    }
}

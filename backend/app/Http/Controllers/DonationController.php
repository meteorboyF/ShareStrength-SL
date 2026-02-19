<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    /**
     * Store a new donation from the landing page form.
     */
    public function store(Request $request)
    {
        // Map the Alpine.js "type" to the database "is_monthly" column
        $request->merge([
            'is_monthly' => $request->type === 'monthly',
            'status' => 'completed', // Set to completed for demo; usually 'pending' until payment is confirmed
        ]);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'is_monthly' => 'required|boolean',
            'currency' => 'nullable|string|size:3',
            'donor_name' => 'nullable|string|max:255',
            'status' => 'required|string',
            'payment_method' => 'nullable|string|max:50',
        ]);

        Donation::create($validated);

        // Redirect back with a success message for the UI
        return redirect()->back()->with('donation_success', 'Thank you! Your donation of $' . $request->amount . ' was received.');
    }

    /**
     * Get financial transparency data for the Chart.
     */
    public function index()
    {
        $monthFunction = DB::getDriverName() === 'sqlite' ? "strftime('%m', created_at)" : 'MONTH(created_at)';
        
        $monthlyRaised = Donation::select(DB::raw('SUM(amount) as total'), DB::raw("$monthFunction as month"))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthFunctionExpense = DB::getDriverName() === 'sqlite' ? "strftime('%m', date)" : 'MONTH(date)';
        
        $monthlyExpenses = Expense::select(DB::raw('SUM(amount) as total'), DB::raw("$monthFunctionExpense as month"))
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = ['labels' => $months, 'raised' => [], 'expenses' => []];

        for ($i = 1; $i <= 12; $i++) {
            // Ensure month is 2 digits for SQLite compatibility (01, 02...) or integer for MySQL
            $key = DB::getDriverName() === 'sqlite' ? str_pad($i, 2, '0', STR_PAD_LEFT) : $i;
            $chartData['raised'][] = $monthlyRaised->get($key, 0);
            $chartData['expenses'][] = $monthlyExpenses->get($key, 0);
        }

        return response()->json(['chart_data' => $chartData]);
    }
}
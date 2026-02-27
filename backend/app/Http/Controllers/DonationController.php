<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    /**
     * Show the checkout page with the selected amount.
     */
    public function checkout(Request $request)
    {
        // Get the amount and type from the URL (e.g., ?amount=50&type=one-time)
        $amount = $request->query('amount', 50);
        $type = $request->query('type', 'one-time');

        // Prevent negative numbers on the backend just in case
        if ($amount < 1) $amount = 50;

        return view('livewire.donate-checkout', compact('amount', 'type'));
    }

    /**
     * Process the payment and save it to the database.
     */
    public function process(Request $request)
    {
        $request->merge([
            'is_monthly' => $request->type === 'monthly',
            'status' => 'completed', // Set to completed for demo
            'payment_method' => 'Credit Card',
        ]);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'is_monthly' => 'required|boolean',
            'status' => 'required|string',
            'payment_method' => 'nullable|string',
        ]);

        Donation::create($validated);

        // Redirect back to the homepage with a success message
        return redirect()->route('home')->with('donation_success', 'Thank you! Your donation of $' . number_format($request->amount, 2) . ' was received.');
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
            $key = DB::getDriverName() === 'sqlite' ? str_pad($i, 2, '0', STR_PAD_LEFT) : $i;
            $chartData['raised'][] = $monthlyRaised->get($key, 0);
            $chartData['expenses'][] = $monthlyExpenses->get($key, 0);
        }

        return response()->json(['chart_data' => $chartData]);
    }
}
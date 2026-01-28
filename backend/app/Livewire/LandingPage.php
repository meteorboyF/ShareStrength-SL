<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class LandingPage extends Component
{
    #[Layout('components.layouts.app')]
    public function render()
    {
        // ... (existing render logic)
        // Calculate totals
        $totalRaised = \App\Models\Donation::sum('amount');
        $totalSpent = \App\Models\Expense::sum('amount');
        
        // Prepare Chart Data (Monthly for current year)
        $monthFunc = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite' 
            ? 'strftime("%m", created_at)' 
            : 'MONTH(created_at)';
            
        $monthlyRaised = \App\Models\Donation::selectRaw("SUM(amount) as total, $monthFunc as month")
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthFuncExpense = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite' 
            ? 'strftime("%m", date)' 
            : 'MONTH(date)';

        $monthlyExpenses = \App\Models\Expense::selectRaw("SUM(amount) as total, $monthFuncExpense as month")
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Fill missing months with 0
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartRaised = [];
        $chartDistributed = [];

        for ($i = 1; $i <= 12; $i++) {
            // Retrieve by integer key $i (MySQL returns int) or padded string (SQLite returns '01')
            $valRaised = $monthlyRaised->get($i) ?? $monthlyRaised->get(sprintf('%02d', $i)) ?? 0;
            $valExpense = $monthlyExpenses->get($i) ?? $monthlyExpenses->get(sprintf('%02d', $i)) ?? 0;
            
            $chartRaised[] = $valRaised;
            $chartDistributed[] = $valExpense;
        }

        return view('livewire.landing-page', [
            'totalRaised' => $totalRaised,
            'totalDistributed' => $totalSpent,
            'chartLabels' => $chartLabels,
            'chartRaised' => $chartRaised,
            'chartDistributed' => $chartDistributed,
        ]);
    }

    public function processDonation($amount, $type)
    {
        // Validate the arguments directly
        \Illuminate\Support\Facades\Validator::make(
            ['amount' => $amount],
            ['amount' => 'required|numeric|min:1']
        )->validate();

        // Create donation record
        $donation = \App\Models\Donation::create([
            'amount' => $amount,
            'currency' => 'USD',
            'donor_name' => 'Anonymous', // In real app, would get from auth or form
            'is_monthly' => $type === 'monthly',
            'status' => 'pending', // Would be 'completed' after payment gateway confirms
            'payment_method' => 'online',
        ]);

        // Use browser event to show alert via JS or redirect
        $this->dispatch('donation-initiated', amount: $amount, type: $type);
        
        // In production, redirect to payment gateway here
        // return redirect()->route('payment.gateway', ['donation' => $donation->id]);
    }
}

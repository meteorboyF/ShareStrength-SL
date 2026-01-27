<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Donations (Monthly data for Jan-Jun 2025)
        $months = [];
        for ($i = 1; $i <= 6; $i++) {
            $months[] = Carbon::create(2025, $i, 1);
        }

        // Realistic donation patterns
        foreach ($months as $month) {
            $daysInMonth = $month->daysInMonth;
            
            // 20-50 donations per month
            $numDonations = rand(20, 50);
            
            for ($j = 0; $j < $numDonations; $j++) {
                Donation::create([
                    'amount' => rand(10, 500),
                    'currency' => 'USD',
                    'donor_name' => $this->randomName(),
                    'is_monthly' => rand(0, 10) > 8, // 20% monthly
                    'created_at' => $month->copy()->addDays(rand(0, $daysInMonth - 1)),
                ]);
            }
        }

        // Seed Expenses
        $expenseCategories = [
            'Medical Aid' => ['Hope Hospital', 'PharmaCare', 'Local Clinic'],
            'Food Supplies' => ['Colombo Food Bank', 'Grocery Wholesalers', 'Community Kitchen'],
            'Education' => ['Rural School Supplies', 'Tech for Kids', 'Book Drive'],
            'Emergency Housing' => ['Flood Relief Fund', 'Shelter Construction', 'Tents & Tarps'],
            'Logistics' => ['Transport Co', 'Fuel Station', 'Vehicle Maintenance'],
        ];

        foreach ($months as $month) {
            $daysInMonth = $month->daysInMonth;
            
            // 10-20 expenses per month
            $numExpenses = rand(10, 20);
            
            for ($k = 0; $k < $numExpenses; $k++) {
                $category = array_rand($expenseCategories);
                $recipients = $expenseCategories[$category];
                $recipient = $recipients[array_rand($recipients)];
                
                Expense::create([
                    'category' => $category,
                    'recipient' => $recipient,
                    'description' => "Covering costs for $category",
                    'amount' => rand(200, 2000), // Expenses are larger chunks
                    'date' => $month->copy()->addDays(rand(0, $daysInMonth - 1)),
                ]);
            }
        }
    }

    private function randomName()
    {
        $names = ['John Doe', 'Jane Smith', 'Michael B', 'Sarah J', 'Anonymous', 'David K', 'Emily R'];
        return $names[array_rand($names)];
    }
}

import React from 'react';
import { Link } from 'react-router-dom';
import { Doughnut } from 'react-chartjs-2';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

// Register Chart.js components
ChartJS.register(ArcElement, Tooltip, Legend);

// --- MOCK DATA ---
const SUMMARY = {
  total_spent: 1250.50,
  to_helpers: 1125.45,
  platform_fees: 125.05
};

const TRANSACTIONS = [
  { id: 104, total: 150.00, fee: 15.00, net: 135.00, status: 'completed', hire_status: 'completed', date: '2025-10-15' },
  { id: 103, total: 45.00, fee: 4.50, net: 40.50, status: 'pending', hire_status: 'active', date: '2025-10-12' },
  { id: 102, total: 200.00, fee: 20.00, net: 180.00, status: 'completed', hire_status: 'completed', date: '2025-10-10' },
  { id: 101, total: 60.00, fee: 6.00, net: 54.00, status: 'failed', hire_status: 'disputed', date: '2025-10-01' },
];

const PaymentHistory = () => {
  // Chart Configuration
  const chartData = {
    labels: ['Paid to Helpers', 'Platform Fees'],
    datasets: [{
      data: [SUMMARY.to_helpers, SUMMARY.platform_fees],
      backgroundColor: ['#4f46e5', '#d1d5db'], // Indigo-600, Gray-300
      borderColor: '#ffffff',
      borderWidth: 2,
    }]
  };

  const chartOptions = {
    cutout: '75%',
    plugins: { legend: { display: false } }
  };

  // Helper for status badges
  const getBadgeColor = (status) => {
    switch (status) {
      case 'completed': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'failed': case 'disputed': return 'bg-red-100 text-red-800';
      case 'active': return 'bg-blue-100 text-blue-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-6 lg:p-8">
      <div className="max-w-7xl mx-auto">
        
        {/* Header */}
        <header className="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
          <div>
            <Link to="/dashboard" className="text-primary hover:text-primary-dark text-sm font-semibold flex items-center gap-1 mb-2">
              &larr; Back to Dashboard
            </Link>
            <h1 className="text-3xl font-extrabold text-neutral-darkest">Payment Dashboard</h1>
            <p className="mt-1 text-neutral-medium">Overview of your payment activity.</p>
          </div>
          <Link to="/payment-insights" className="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition shadow-sm flex items-center gap-2">
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            View Detailed Analysis
          </Link>
        </header>

        {/* Top Section: Summaries & Chart */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
          
          {/* Summary Cards */}
          <div className="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up">
              <p className="text-sm font-medium text-neutral-medium">Total Spent</p>
              <p className="mt-1 text-3xl font-bold text-neutral-darkest">${SUMMARY.total_spent.toFixed(2)}</p>
            </div>
            <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up" style={{animationDelay: '100ms'}}>
              <p className="text-sm font-medium text-neutral-medium">Paid to Helpers</p>
              <p className="mt-1 text-3xl font-bold text-neutral-darkest">${SUMMARY.to_helpers.toFixed(2)}</p>
            </div>
            <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up" style={{animationDelay: '200ms'}}>
              <p className="text-sm font-medium text-neutral-medium">Platform Fees</p>
              <p className="mt-1 text-3xl font-bold text-neutral-darkest">${SUMMARY.platform_fees.toFixed(2)}</p>
            </div>
          </div>

          {/* Chart Card */}
          <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm flex flex-col items-center justify-center animate-fade-in-up" style={{animationDelay: '300ms'}}>
             <h3 className="text-sm font-semibold text-neutral-darkest mb-4">Spending Overview</h3>
             <div className="w-40 h-40">
                <Doughnut data={chartData} options={chartOptions} />
             </div>
          </div>
        </div>

        {/* Transaction Table */}
        <div className="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden animate-fade-in-up" style={{animationDelay: '400ms'}}>
            <div className="p-6 border-b border-neutral-100">
                <h2 className="text-xl font-bold text-neutral-darkest">Transaction History</h2>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-sm text-left">
                    <thead className="bg-neutral-light text-neutral-medium">
                        <tr>
                            <th className="px-6 py-3 font-medium">ID</th>
                            <th className="px-6 py-3 font-medium">Date</th>
                            <th className="px-6 py-3 font-medium">Total</th>
                            <th className="px-6 py-3 font-medium">Net (Helper)</th>
                            <th className="px-6 py-3 font-medium">Payment</th>
                            <th className="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-neutral-100">
                        {TRANSACTIONS.map((tx) => (
                            <tr key={tx.id} className="hover:bg-neutral-50">
                                <td className="px-6 py-4 font-mono text-neutral-500">#{tx.id}</td>
                                <td className="px-6 py-4 text-neutral-dark">{tx.date}</td>
                                <td className="px-6 py-4 font-bold text-neutral-darkest">${tx.total.toFixed(2)}</td>
                                <td className="px-6 py-4 text-neutral-dark">${tx.net.toFixed(2)}</td>
                                <td className="px-6 py-4">
                                    <span className={`px-2 py-1 rounded-full text-xs font-bold ${getBadgeColor(tx.status)}`}>
                                        {tx.status.toUpperCase()}
                                    </span>
                                </td>
                                <td className="px-6 py-4">
                                    <span className={`px-2 py-1 rounded-full text-xs font-bold ${getBadgeColor(tx.hire_status)}`}>
                                        {tx.hire_status.toUpperCase()}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>

      </div>
    </div>
  );
};

export default PaymentHistory;
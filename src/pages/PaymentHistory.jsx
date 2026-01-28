import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Bar, Doughnut } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement } from 'chart.js';
import api from '../services/api';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement);

const PaymentHistory = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchInsights();
  }, []);

  const fetchInsights = async () => {
    try {
      const res = await api.get('/payments/insights');
      setData(res.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="min-h-screen flex items-center justify-center"><div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div></div>;
  }

  if (!data) return <div className="text-center p-8">Failed to load data.</div>;

  // Chart Data Preparation
  const monthlyChartData = {
    labels: data.monthly?.labels || [],
    datasets: [
      {
        label: 'Spending ($)',
        data: data.monthly?.values || [],
        backgroundColor: '#4f46e5',
        borderRadius: 4,
      },
    ],
  };

  const monthlyChartOptions = {
    responsive: true,
    plugins: {
      legend: { position: 'top' },
      title: { display: false },
    },
    scales: {
      y: { beginAtZero: true }
    }
  };

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-6 lg:p-8">
      <div className="max-w-7xl mx-auto space-y-8">

        {/* Header */}
        <header className="flex flex-col md:flex-row md:items-end justify-between gap-4 animate-fade-in-up">
          <div>
            <Link to="/dashboard" className="text-primary hover:text-primary-dark text-sm font-semibold flex items-center gap-1 mb-2">
              &larr; Back to Dashboard
            </Link>
            <h1 className="text-3xl font-extrabold text-neutral-darkest">Payment History & Analysis</h1>
            <p className="mt-1 text-neutral-medium">Track your spending by time, task, and helper.</p>
          </div>
        </header>

        {/* 1. Spending Summaries (1m, 6m, 1y) */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up" style={{ animationDelay: '100ms' }}>
          <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <p className="text-sm font-medium text-neutral-500 uppercase tracking-wide">Last 1 Month</p>
            <p className="mt-2 text-3xl font-bold text-neutral-darkest">${data.spending_summary?.last_1_month?.toFixed(2) || '0.00'}</p>
            <div className="mt-2 text-xs text-green-600 font-medium">Recent Spending</div>
          </div>
          <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <p className="text-sm font-medium text-neutral-500 uppercase tracking-wide">Last 6 Months</p>
            <p className="mt-2 text-3xl font-bold text-neutral-darkest">${data.spending_summary?.last_6_months?.toFixed(2) || '0.00'}</p>
            <div className="mt-2 text-xs text-blue-600 font-medium">Mid-term Analysis</div>
          </div>
          <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <p className="text-sm font-medium text-neutral-500 uppercase tracking-wide">Last 1 Year</p>
            <p className="mt-2 text-3xl font-bold text-neutral-darkest">${data.spending_summary?.last_1_year?.toFixed(2) || '0.00'}</p>
            <div className="mt-2 text-xs text-purple-600 font-medium">Long-term Total</div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in-up" style={{ animationDelay: '200ms' }}>
          {/* 2. Monthly Chart */}
          <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <h3 className="text-lg font-bold text-neutral-darkest mb-4">Monthly Spending Trend</h3>
            <div className="h-64">
              <Bar data={monthlyChartData} options={monthlyChartOptions} />
            </div>
          </div>

          {/* 3. Top Helpers */}
          <div className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <h3 className="text-lg font-bold text-neutral-darkest mb-4">Spending by Helper</h3>
            <div className="space-y-4">
              {data.helpers?.length === 0 ? <p className="text-neutral-500">No data available.</p> :
                data.helpers?.map((helper, idx) => (
                  <div key={idx} className="flex items-center justify-between p-3 bg-neutral-50 rounded-lg">
                    <span className="font-semibold text-neutral-700">{helper.name}</span>
                    <span className="font-bold text-neutral-darkest">${parseFloat(helper.amount).toFixed(2)}</span>
                  </div>
                ))}
            </div>
          </div>
        </div>

        {/* 4. Task History */}
        <div className="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden animate-fade-in-up" style={{ animationDelay: '300ms' }}>
          <div className="p-6 border-b border-neutral-100">
            <h2 className="text-xl font-bold text-neutral-darkest">Task-wise Payment History</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="bg-neutral-light text-neutral-medium">
                <tr>
                  <th className="px-6 py-3 font-medium">Date</th>
                  <th className="px-6 py-3 font-medium">Task Title</th>
                  <th className="px-6 py-3 font-medium">Helper</th>
                  <th className="px-6 py-3 font-medium text-right">Amount</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-100">
                {data.tasks?.map((tx, i) => (
                  <tr key={i} className="hover:bg-neutral-50">
                    <td className="px-6 py-4 text-neutral-500">{tx.date}</td>
                    <td className="px-6 py-4 font-medium text-neutral-darkest">{tx.title}</td>
                    <td className="px-6 py-4 text-neutral-dark">{tx.helper}</td>
                    <td className="px-6 py-4 font-bold text-neutral-darkest text-right">${parseFloat(tx.fee).toFixed(2)}</td>
                  </tr>
                ))}
                {(!data.tasks || data.tasks.length === 0) && (
                  <tr>
                    <td colSpan="4" className="px-6 py-8 text-center text-neutral-500">No transaction records found.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  );
};

export default PaymentHistory;
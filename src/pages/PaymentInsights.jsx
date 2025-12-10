import React from 'react';
import { Link } from 'react-router-dom';
import { Bar, Doughnut } from 'react-chartjs-2';
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  BarElement, 
  Title, 
  Tooltip, 
  Legend, 
  ArcElement 
} from 'chart.js';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement);

// --- MOCK DATA ---
const MONTHLY_DATA = {
  labels: ['Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025'],
  values: [450, 320, 500, 150]
};

const HELPER_DATA = [
  { name: 'Sarah Smith', amount: 800 },
  { name: 'Mike Ross', amount: 450 },
  { name: 'John Doe', amount: 170 }
];

const TASK_BREAKDOWN = [
  { title: "Math Tutoring", helper: "Sarah Smith", fee: 150.00 },
  { title: "Grocery Shopping", helper: "Mike Ross", fee: 55.50 },
  { title: "Garden Cleaning", helper: "John Doe", fee: 40.00 },
  { title: "Math Tutoring", helper: "Sarah Smith", fee: 150.00 },
];

const PaymentInsights = () => {
  
  // Bar Chart Config
  const barData = {
    labels: MONTHLY_DATA.labels,
    datasets: [{
      label: 'Total Spent',
      data: MONTHLY_DATA.values,
      backgroundColor: '#6366f1', // Indigo-500
      borderRadius: 4,
    }]
  };

  const barOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { callback: (val) => '$' + val } } }
  };

  // Doughnut Config
  const doughnutData = {
    labels: HELPER_DATA.map(h => h.name),
    datasets: [{
      data: HELPER_DATA.map(h => h.amount),
      backgroundColor: ['#4f46e5', '#818cf8', '#c7d2fe'],
      borderWidth: 0
    }]
  };

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-6 lg:p-8">
      <div className="max-w-7xl mx-auto space-y-8">
        
        {/* Header */}
        <header className="animate-fade-in-up">
            <Link to="/payment" className="text-primary hover:text-primary-dark text-sm font-semibold mb-2 inline-block">
                &larr; Back to Payment History
            </Link>
            <h1 className="text-3xl font-extrabold text-neutral-darkest">Detailed Payment Analysis</h1>
            <p className="mt-1 text-neutral-medium">In-depth look at your spending habits.</p>
        </header>

        {/* Monthly Bar Chart */}
        <section className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up" style={{animationDelay: '100ms'}}>
            <h2 className="text-xl font-bold text-neutral-darkest mb-6">Monthly Spending</h2>
            <div className="h-80 w-full">
                <Bar data={barData} options={barOptions} />
            </div>
        </section>

        {/* Split View: Helper Breakdown & Task Table */}
        <div className="grid grid-cols-1 lg:grid-cols-5 gap-8">
            
            {/* Helper Breakdown (Doughnut) */}
            <div className="lg:col-span-2 bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up" style={{animationDelay: '200ms'}}>
                <h2 className="text-xl font-bold text-neutral-darkest mb-6">Spending by Helper</h2>
                <div className="h-56 w-56 mx-auto mb-6">
                    <Doughnut data={doughnutData} options={{ cutout: '70%', plugins: { legend: { display: false } } }} />
                </div>
                <ul className="space-y-3">
                    {HELPER_DATA.map((h, i) => (
                        <li key={i} className="flex justify-between items-center text-sm border-b border-neutral-light last:border-0 pb-2 last:pb-0">
                            <span className="font-medium text-neutral-dark">{h.name}</span>
                            <span className="font-bold text-neutral-darkest">${h.amount.toFixed(2)}</span>
                        </li>
                    ))}
                </ul>
            </div>

            {/* Task Table */}
            <div className="lg:col-span-3 bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden animate-fade-in-up" style={{animationDelay: '300ms'}}>
                <div className="p-6 border-b border-neutral-100">
                    <h2 className="text-xl font-bold text-neutral-darkest">Spending by Task</h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-neutral-light text-neutral-medium">
                            <tr>
                                <th className="px-6 py-3 font-medium">Task</th>
                                <th className="px-6 py-3 font-medium">Helper</th>
                                <th className="px-6 py-3 font-medium text-right">Total Fee</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-neutral-100">
                            {TASK_BREAKDOWN.map((t, i) => (
                                <tr key={i} className="hover:bg-neutral-50">
                                    <td className="px-6 py-4 font-bold text-neutral-darkest">{t.title}</td>
                                    <td className="px-6 py-4 text-neutral-medium">{t.helper}</td>
                                    <td className="px-6 py-4 font-mono text-neutral-dark text-right">${t.fee.toFixed(2)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  );
};

export default PaymentInsights;
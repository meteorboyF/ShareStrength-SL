import React from 'react';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import { Bar } from 'react-chartjs-2';
import { ShieldCheck, TrendingUp, PieChart } from 'lucide-react';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

import api from '../services/api';

const TransparencySection = () => {
    const [stats, setStats] = React.useState({
        total_raised: 0,
        funds_used_percentage: 0,
        remaining_funds: 0,
        chart_data: { labels: [], raised: [], expenses: [] },
        recent_expenses: []
    });

    React.useEffect(() => {
        const fetchTransparencyData = async () => {
            try {
                const response = await api.get('/financial-transparency');
                setStats(response.data);
            } catch (error) {
                console.error('Failed to fetch transparency data:', error);
            }
        };

        fetchTransparencyData();
    }, []);

    const chartData = {
        labels: stats.chart_data.labels.length > 0 ? stats.chart_data.labels : ['Jan', 'Feb', 'Mar'],
        datasets: [
            {
                label: 'Funds Raised',
                data: stats.chart_data.raised,
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Funds Distributed',
                data: stats.chart_data.expenses,
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 4,
            },
        ],
    };

    const options = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Monthly Financial Overview (2025)',
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: false,
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
    };

    const handleViewReport = () => {
        // Mock download or detailed view
        const reportData = JSON.stringify(stats, null, 2);
        const blob = new Blob([reportData], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'financial_report_2025.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        alert('Downloading full financial report...');
    };

    return (
        <section className="py-20 bg-white">
            <div className="container mx-auto px-4">
                <div className="text-center mb-16">
                    <div className="inline-flex items-center justify-center p-2 px-4 bg-green-50 rounded-full text-green-700 font-semibold mb-4 mx-auto">
                        <ShieldCheck className="w-5 h-5 mr-2" />
                        100% Transparent
                    </div>
                    <h2 className="text-3xl md:text-5xl font-bold text-neutral-dark mb-4">
                        Where Your Money Goes
                    </h2>
                    <p className="text-xl text-neutral-600 max-w-2xl mx-auto">
                        We believe in radical transparency. Track every dollar from donation to impact.
                    </p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    {/* Chart Area */}
                    <div className="bg-white p-6 rounded-3xl shadow-lg border border-neutral-100">
                        <Bar options={options} data={chartData} />
                    </div>

                    {/* Stats & Transactions */}
                    <div className="space-y-8">
                        {/* Stats Grid */}
                        <div className="grid grid-cols-2 gap-4">
                            <div className="p-6 bg-neutral-50 rounded-2xl">
                                <div className="text-neutral-500 mb-2 flex items-center"><TrendingUp className="w-4 h-4 mr-2" /> Total Raised</div>
                                <div className="text-3xl font-bold text-neutral-800">${parseInt(stats.total_raised).toLocaleString()}</div>
                            </div>
                            <div className="p-6 bg-neutral-50 rounded-2xl">
                                <div className="text-neutral-500 mb-2 flex items-center"><PieChart className="w-4 h-4 mr-2" /> Funds Used</div>
                                <div className="text-3xl font-bold text-green-600">{stats.funds_used_percentage}%</div>
                            </div>
                        </div>

                        {/* Recent Transactions List */}
                        <div className="bg-white border border-neutral-100 rounded-2xl overflow-hidden shadow-sm">
                            <div className="p-4 bg-neutral-50 border-b border-neutral-100 font-semibold text-neutral-700">
                                Recent Distributions
                            </div>
                            <div className="divide-y divide-neutral-100">
                                {stats.recent_expenses.length > 0 ? stats.recent_expenses.map((tx) => (
                                    <div key={tx.id} className="p-4 flex items-center justify-between hover:bg-neutral-50 transition-colors">
                                        <div>
                                            <div className="font-medium text-neutral-800">{tx.category}</div>
                                            <div className="text-sm text-neutral-500">{tx.recipient} • {new Date(tx.date).toLocaleDateString()}</div>
                                        </div>
                                        <div className="font-bold text-neutral-800">${parseInt(tx.amount).toLocaleString()}</div>
                                    </div>
                                )) : <div className="p-4 text-center text-neutral-500">Loading data...</div>}
                            </div>
                            <div className="p-3 text-center border-t border-neutral-100">
                                <button
                                    onClick={handleViewReport}
                                    className="text-primary font-medium hover:underline text-sm"
                                >
                                    View Full Financial Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default TransparencySection;

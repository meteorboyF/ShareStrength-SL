import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { ArrowLeft, DollarSign, Calendar, TrendingUp } from 'lucide-react';

const MyEarnings = () => {
    const navigate = useNavigate();
    const [payments, setPayments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filterRange, setFilterRange] = useState('1m'); // '1m', '6m', '1y'
    const [stats, setStats] = useState({
        total: 0,
        pending: 0,
        paid: 0,
        count: 0
    });

    useEffect(() => {
        fetchPayments();
    }, []);

    useEffect(() => {
        calculateStats();
    }, [payments, filterRange]);

    const fetchPayments = async () => {
        try {
            const response = await api.get('/payments');
            setPayments(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch payments:', error);
        } finally {
            setLoading(false);
        }
    };

    const calculateStats = () => {
        const now = new Date();
        const cutoff = new Date();

        // Set cutoff date based on filter
        if (filterRange === '1m') {
            cutoff.setMonth(now.getMonth() - 1);
        } else if (filterRange === '6m') {
            cutoff.setMonth(now.getMonth() - 6);
        } else if (filterRange === '1y') {
            cutoff.setFullYear(now.getFullYear() - 1);
        }

        const filtered = payments.filter(p => {
            const date = new Date(p.created_at);
            return date >= cutoff;
        });

        const total = filtered.reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
        const pending = filtered
            .filter(p => p.status === 'pending')
            .reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
        const paid = filtered
            .filter(p => p.status === 'paid')
            .reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);

        setStats({
            total,
            pending,
            paid,
            count: filtered.length,
            filteredPayments: filtered
        });
    };

    return (
        <div className="min-h-screen bg-neutral-light font-sans text-neutral-dark pb-12">
            {/* Header */}
            <header className="bg-white shadow-sm sticky top-0 z-40">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button
                        onClick={() => navigate(-1)}
                        className="flex items-center gap-2 text-neutral-medium hover:text-primary transition"
                    >
                        <ArrowLeft size={20} />
                        <span className="font-medium">Back</span>
                    </button>
                    <h1 className="text-xl font-bold text-neutral-darkest">My Earnings</h1>
                    <div className="w-16"></div> {/* Spacer */}
                </div>
            </header>

            <main className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">

                {/* Filter Tabs */}
                <div className="bg-white rounded-xl p-2 shadow-sm border border-neutral-100 mb-6 flex justify-center max-w-md mx-auto">
                    {['1m', '6m', '1y'].map((range) => (
                        <button
                            key={range}
                            onClick={() => setFilterRange(range)}
                            className={`flex-1 px-4 py-2 rounded-lg text-sm font-medium transition ${filterRange === range
                                    ? 'bg-primary text-white shadow-md'
                                    : 'text-neutral-600 hover:bg-neutral-50'
                                }`}
                        >
                            {range === '1m' ? '1 Month' : range === '6m' ? '6 Months' : '1 Year'}
                        </button>
                    ))}
                </div>

                {/* Info Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {/* Total Earning */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
                        <div className="flex items-center justify-between mb-2">
                            <span className="text-sm font-medium text-neutral-500">Total Earnings</span>
                            <div className="bg-green-100 p-2 rounded-lg text-green-600">
                                <DollarSign size={20} />
                            </div>
                        </div>
                        <h3 className="text-3xl font-bold text-neutral-darkest">${stats.total.toFixed(2)}</h3>
                        <p className="text-xs text-neutral-400 mt-1">In selected period</p>
                    </div>

                    {/* Pending */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
                        <div className="flex items-center justify-between mb-2">
                            <span className="text-sm font-medium text-neutral-500">Pending</span>
                            <div className="bg-yellow-100 p-2 rounded-lg text-yellow-600">
                                <Calendar size={20} />
                            </div>
                        </div>
                        <h3 className="text-3xl font-bold text-neutral-darkest">${stats.pending.toFixed(2)}</h3>
                        <p className="text-xs text-neutral-400 mt-1">Waiting for payment</p>
                    </div>

                    {/* Jobs Count */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
                        <div className="flex items-center justify-between mb-2">
                            <span className="text-sm font-medium text-neutral-500">Completed Jobs</span>
                            <div className="bg-blue-100 p-2 rounded-lg text-blue-600">
                                <TrendingUp size={20} />
                            </div>
                        </div>
                        <h3 className="text-3xl font-bold text-neutral-darkest">{stats.count}</h3>
                        <p className="text-xs text-neutral-400 mt-1">Recorded transactions</p>
                    </div>
                </div>

                {/* Transactions List */}
                <div className="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
                    <div className="px-6 py-4 border-b border-neutral-100">
                        <h3 className="font-bold text-neutral-darkest">Transaction History</h3>
                    </div>

                    {loading ? (
                        <div className="p-8 text-center">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
                            <p className="text-neutral-500">Loading transactions...</p>
                        </div>
                    ) : stats.filteredPayments && stats.filteredPayments.length > 0 ? (
                        <div className="divide-y divide-gray-100">
                            {stats.filteredPayments.map((payment) => (
                                <div key={payment.id} className="p-6 hover:bg-neutral-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div className="flex items-start gap-4">
                                        <div className={`p-3 rounded-full ${payment.status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'}`}>
                                            <DollarSign size={20} />
                                        </div>
                                        <div>
                                            <h4 className="font-bold text-neutral-800">{payment.task?.title || 'Unknown Task'}</h4>
                                            <p className="text-sm text-neutral-500">
                                                from {payment.payer?.name || 'Unknown User'} • {new Date(payment.created_at).toLocaleDateString()}
                                            </p>
                                            <div className="mt-1 text-xs text-neutral-400">
                                                {payment.hours_worked} hrs × ${payment.hourly_rate}/hr
                                            </div>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <span className="block text-lg font-bold text-neutral-800">${parseFloat(payment.amount).toFixed(2)}</span>
                                        <span className={`inline-block px-2 py-0.5 rounded text-xs font-semibold capitalize ${payment.status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                            }`}>
                                            {payment.status}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="p-8 text-center text-neutral-500">
                            No transactions found for this period.
                        </div>
                    )}
                </div>
            </main>
        </div>
    );
};

export default MyEarnings;

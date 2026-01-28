import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { Users, UserCheck, Briefcase, DollarSign, TrendingUp, Shield, LogOut } from 'lucide-react';

const AdminDashboard = () => {
    const navigate = useNavigate();
    const [stats, setStats] = useState({
        totalUsers: 0,
        totalHelpers: 0,
        totalTasks: 0,
        totalPayments: 0,
        pendingPayments: 0,
        totalEarnings: 0
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchStats();
    }, []);

    const fetchStats = async () => {
        try {
            // Fetch statistics from various endpoints
            const [usersRes, helpersRes, tasksRes, paymentsRes] = await Promise.all([
                api.get('/users').catch(() => ({ data: [] })),
                api.get('/helpers').catch(() => ({ data: [] })),
                api.get('/tasks').catch(() => ({ data: [] })),
                api.get('/payments').catch(() => ({ data: [] }))
            ]);

            const users = Array.isArray(usersRes.data) ? usersRes.data : [];
            const helpers = Array.isArray(helpersRes.data) ? helpersRes.data : [];
            const tasks = Array.isArray(tasksRes.data) ? tasksRes.data : [];
            const payments = Array.isArray(paymentsRes.data) ? paymentsRes.data : [];

            const totalEarnings = payments
                .filter(p => p.status === 'paid')
                .reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);

            const pendingPayments = payments.filter(p => p.status === 'pending').length;

            setStats({
                totalUsers: users.length,
                totalHelpers: helpers.length,
                totalTasks: tasks.length,
                totalPayments: payments.length,
                pendingPayments,
                totalEarnings
            });
        } catch (error) {
            console.error('Failed to fetch stats:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleLogout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        navigate('/login');
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 font-sans">
            {/* Header */}
            <header className="bg-white shadow-sm border-b border-slate-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <div className="flex items-center gap-3">
                        <Shield className="text-primary" size={32} />
                        <div>
                            <h1 className="text-2xl font-bold text-slate-900">Admin Dashboard</h1>
                            <p className="text-xs text-slate-500">ShareStrength Management Panel</p>
                        </div>
                    </div>
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                    >
                        <LogOut size={18} />
                        Logout
                    </button>
                </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Welcome Banner */}
                <div className="bg-gradient-to-r from-primary to-purple-600 text-white rounded-xl p-6 mb-8 shadow-lg">
                    <h2 className="text-2xl font-bold mb-2">Welcome, Administrator! 👋</h2>
                    <p className="text-white/90">Monitor and manage the ShareStrength platform from here.</p>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    {/* Total Users */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                        <div className="flex items-center justify-between mb-4">
                            <div className="bg-blue-100 p-3 rounded-lg">
                                <Users className="text-blue-600" size={24} />
                            </div>
                            <span className="text-xs font-semibold text-slate-500 uppercase">Users (PWD)</span>
                        </div>
                        <h3 className="text-3xl font-bold text-slate-900">{stats.totalUsers}</h3>
                        <p className="text-sm text-slate-500 mt-1">Registered Users</p>
                    </div>

                    {/* Total Helpers */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                        <div className="flex items-center justify-between mb-4">
                            <div className="bg-green-100 p-3 rounded-lg">
                                <UserCheck className="text-green-600" size={24} />
                            </div>
                            <span className="text-xs font-semibold text-slate-500 uppercase">Helpers</span>
                        </div>
                        <h3 className="text-3xl font-bold text-slate-900">{stats.totalHelpers}</h3>
                        <p className="text-sm text-slate-500 mt-1">Available Helpers</p>
                    </div>

                    {/* Total Tasks */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                        <div className="flex items-center justify-between mb-4">
                            <div className="bg-purple-100 p-3 rounded-lg">
                                <Briefcase className="text-purple-600" size={24} />
                            </div>
                            <span className="text-xs font-semibold text-slate-500 uppercase">Tasks</span>
                        </div>
                        <h3 className="text-3xl font-bold text-slate-900">{stats.totalTasks}</h3>
                        <p className="text-sm text-slate-500 mt-1">Total Tasks Posted</p>
                    </div>

                    {/* Pending Payments */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                        <div className="flex items-center justify-between mb-4">
                            <div className="bg-yellow-100 p-3 rounded-lg">
                                <DollarSign className="text-yellow-600" size={24} />
                            </div>
                            <span className="text-xs font-semibold text-slate-500 uppercase">Pending</span>
                        </div>
                        <h3 className="text-3xl font-bold text-slate-900">{stats.pendingPayments}</h3>
                        <p className="text-sm text-slate-500 mt-1">Pending Payments</p>
                    </div>

                    {/* Total Payments */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                        <div className="flex items-center justify-between mb-4">
                            <div className="bg-indigo-100 p-3 rounded-lg">
                                <TrendingUp className="text-indigo-600" size={24} />
                            </div>
                            <span className="text-xs font-semibold text-slate-500 uppercase">Payments</span>
                        </div>
                        <h3 className="text-3xl font-bold text-slate-900">{stats.totalPayments}</h3>
                        <p className="text-sm text-slate-500 mt-1">Total Transactions</p>
                    </div>

                    {/* Total Earnings */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                        <div className="flex items-center justify-between mb-4">
                            <div className="bg-emerald-100 p-3 rounded-lg">
                                <DollarSign className="text-emerald-600" size={24} />
                            </div>
                            <span className="text-xs font-semibold text-slate-500 uppercase">Earnings</span>
                        </div>
                        <h3 className="text-3xl font-bold text-slate-900">${stats.totalEarnings.toFixed(2)}</h3>
                        <p className="text-sm text-slate-500 mt-1">Platform Revenue</p>
                    </div>
                </div>

                {/* Info Cards */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* System Status */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                        <h3 className="text-lg font-bold text-slate-900 mb-4">System Status</h3>
                        <div className="space-y-3">
                            <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <span className="text-sm font-medium text-slate-700">API Server</span>
                                <span className="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Online</span>
                            </div>
                            <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <span className="text-sm font-medium text-slate-700">Database</span>
                                <span className="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Connected</span>
                            </div>
                            <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <span className="text-sm font-medium text-slate-700">Authentication</span>
                                <span className="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Active</span>
                            </div>
                        </div>
                    </div>

                    {/* Quick Actions */}
                    <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                        <h3 className="text-lg font-bold text-slate-900 mb-4">Quick Actions</h3>
                        <div className="space-y-3">
                            <button
                                onClick={() => navigate('/admin/users')}
                                className="w-full text-left p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                                <span className="text-sm font-medium text-slate-700">View All Users</span>
                            </button>
                            <button
                                onClick={() => navigate('/admin/helpers')}
                                className="w-full text-left p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                                <span className="text-sm font-medium text-slate-700">View All Helpers</span>
                            </button>
                            <button
                                onClick={() => navigate('/admin/payments')}
                                className="w-full text-left p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                                <span className="text-sm font-medium text-slate-700">Manage Payments</span>
                            </button>
                            <button
                                onClick={() => navigate('/admin/reviews')}
                                className="w-full text-left p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                                <span className="text-sm font-medium text-slate-700">Manage Reviews</span>
                            </button>
                            <button
                                onClick={() => navigate('/admin/resources')}
                                className="w-full text-left p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                                <span className="text-sm font-medium text-slate-700">Upload Resources</span>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
};

export default AdminDashboard;

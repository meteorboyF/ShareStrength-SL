import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import { ArrowLeft, DollarSign, Filter } from 'lucide-react';

const PaymentManagement = () => {
    const navigate = useNavigate();
    const [payments, setPayments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filterStatus, setFilterStatus] = useState('all');

    useEffect(() => {
        fetchPayments();
    }, [filterStatus]);

    const fetchPayments = async () => {
        try {
            const params = {};
            if (filterStatus !== 'all') params.status = filterStatus;
            const response = await api.get('/admin/payments', { params });
            setPayments(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch payments:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 pb-12">
            <header className="bg-white shadow-sm border-b">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button onClick={() => navigate(-1)} className="flex items-center gap-2 text-slate-600 hover:text-primary">
                        <ArrowLeft size={20} />
                        Back
                    </button>
                    <div className="flex items-center gap-2">
                        <DollarSign className="text-green-600" size={24} />
                        <h1 className="text-xl font-bold">Payment Management</h1>
                    </div>
                    <div className="w-16"></div>
                </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div className="bg-white rounded-xl p-6 shadow-sm border mb-6">
                    <select value={filterStatus} onChange={(e) => setFilterStatus(e.target.value)}
                        className="px-4 py-2 border rounded-lg">
                        <option value="all">All Payments</option>
                        <option value="paid">Paid Only</option>
                        <option value="pending">Pending Only</option>
                    </select>
                </div>

                <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
                    {loading ? (
                        <div className="flex justify-center items-center py-12">
                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                        </div>
                    ) : (
                        <table className="w-full">
                            <thead className="bg-slate-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">ID</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Task</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">User</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Helper</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Amount</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Status</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {payments.length === 0 ? (
                                    <tr><td colSpan="7" className="px-6 py-12 text-center text-slate-500">No payments found</td></tr>
                                ) : (
                                    payments.map((payment) => (
                                        <tr key={payment.id} className="hover:bg-slate-50">
                                            <td className="px-6 py-4 text-sm">{payment.id}</td>
                                            <td className="px-6 py-4 text-sm">{payment.task?.title || 'N/A'}</td>
                                            <td className="px-6 py-4 text-sm">{payment.payer?.name || 'N/A'}</td>
                                            <td className="px-6 py-4 text-sm">{payment.payee?.name || 'N/A'}</td>
                                            <td className="px-6 py-4 text-sm font-bold">${parseFloat(payment.amount || 0).toFixed(2)}</td>
                                            <td className="px-6 py-4">
                                                <span className={`px-2 py-1 text-xs font-semibold rounded-full ${payment.status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
                                                    {payment.status}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm text-slate-600">{new Date(payment.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    )}
                </div>
            </main>
        </div>
    );
};

export default PaymentManagement;

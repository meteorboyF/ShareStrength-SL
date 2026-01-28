import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import { ArrowLeft, Search, Filter, Shield, Ban, CheckCircle, Users } from 'lucide-react';

const UserManagement = () => {
    const navigate = useNavigate();
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [filterStatus, setFilterStatus] = useState('all');
    const [filterSuspended, setFilterSuspended] = useState('all');

    useEffect(() => {
        fetchUsers();
    }, [searchTerm, filterStatus, filterSuspended]);

    const fetchUsers = async () => {
        try {
            const params = {};
            if (searchTerm) params.search = searchTerm;
            if (filterStatus !== 'all') params.verification_status = filterStatus;
            if (filterSuspended !== 'all') params.is_suspended = filterSuspended === 'suspended';

            const response = await api.get('/admin/users', { params });
            setUsers(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch users:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleVerify = async (userId) => {
        if (!confirm('Verify this user?')) return;
        try {
            await api.put(`/admin/users/${userId}/verify`);
            alert('User verified successfully');
            fetchUsers();
        } catch (error) {
            alert('Failed to verify user');
        }
    };

    const handleSuspend = async (userId, shouldSuspend = true) => {
        if (!confirm(shouldSuspend ? 'Suspend this user?' : 'Unsuspend this user?')) return;
        try {
            await api.put(`/admin/users/${userId}/suspend`, { suspend: shouldSuspend });
            alert(shouldSuspend ? 'User suspended' : 'User unsuspended');
            fetchUsers();
        } catch (error) {
            alert('Action failed');
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 pb-12">
            {/* Header */}
            <header className="bg-white shadow-sm border-b">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button onClick={() => navigate(-1)} className="flex items-center gap-2 text-slate-600 hover:text-primary">
                        <ArrowLeft size={20} />
                        <span className="font-medium">Back</span>
                    </button>
                    <div className="flex items-center gap-2">
                        <Users className="text-primary" size={24} />
                        <h1 className="text-xl font-bold text-slate-900">User Management</h1>
                    </div>
                    <div className="w-16"></div>
                </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Search & Filter Bar */}
                <div className="bg-white rounded-xl p-6 shadow-sm border mb-6">
                    <div className="flex flex-col md:flex-row gap-4">
                        {/* Search */}
                        <div className="flex-1 relative">
                            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" size={20} />
                            <input
                                type="text"
                                placeholder="Search by name or email..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            />
                        </div>

                        {/* Filters */}
                        <div className="flex gap-4">
                            <select
                                value={filterStatus}
                                onChange={(e) => setFilterStatus(e.target.value)}
                                className="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="all">All Status</option>
                                <option value="verified">Verified</option>
                                <option value="unverified">Unverified</option>
                            </select>

                            <select
                                value={filterSuspended}
                                onChange={(e) => setFilterSuspended(e.target.value)}
                                className="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="all">All Users</option>
                                <option value="active">Active Only</option>
                                <option value="suspended">Suspended Only</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Users Table */}
                <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
                    {loading ? (
                        <div className="flex justify-center items-center py-12">
                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-slate-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">ID</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Name</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Email</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Status</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y">
                                    {users.length === 0 ? (
                                        <tr>
                                            <td colSpan="5" className="px-6 py-12 text-center text-slate-500">
                                                No users found
                                            </td>
                                        </tr>
                                    ) : (
                                        users.map((user) => (
                                            <tr key={user.id} className="hover:bg-slate-50">
                                                <td className="px-6 py-4 text-sm text-slate-900">{user.id}</td>
                                                <td className="px-6 py-4 text-sm font-medium text-slate-900">{user.name}</td>
                                                <td className="px-6 py-4 text-sm text-slate-600">{user.email}</td>
                                                <td className="px-6 py-4">
                                                    <div className="flex gap-2">
                                                        <span className={`px-2 py-1 text-xs font-semibold rounded-full ${user.verification_status === 'verified' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
                                                            {user.verification_status || 'Unverified'}
                                                        </span>
                                                        {user.is_suspended && (
                                                            <span className="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                                Suspended
                                                            </span>
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex gap-2">
                                                        {user.verification_status !== 'verified' && (
                                                            <button
                                                                onClick={() => handleVerify(user.id)}
                                                                className="flex items-center gap-1 px-3 py-1 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 text-xs font-medium">
                                                                <CheckCircle size={14} />
                                                                Verify
                                                            </button>
                                                        )}
                                                        {user.is_suspended ? (
                                                            <button
                                                                onClick={() => handleSuspend(user.id, false)}
                                                                className="flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 text-xs font-medium">
                                                                <Shield size={14} />
                                                                Unsuspend
                                                            </button>
                                                        ) : (
                                                            <button
                                                                onClick={() => handleSuspend(user.id, true)}
                                                                className="flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 text-xs font-medium">
                                                                <Ban size={14} />
                                                                Suspend
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </main>
        </div>
    );
};

export default UserManagement;

import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import { ArrowLeft, Search, Users, CheckCircle, Ban, Shield, Star } from 'lucide-react';

const HelperManagement = () => {
    const navigate = useNavigate();
    const [helpers, setHelpers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [filterVerified, setFilterVerified] = useState('all');
    const [filterSuspended, setFilterSuspended] = useState('all');

    useEffect(() => {
        fetchHelpers();
    }, [searchTerm, filterVerified, filterSuspended]);

    const fetchHelpers = async () => {
        try {
            const params = {};
            if (searchTerm) params.search = searchTerm;
            if (filterVerified !== 'all') params.is_verified = filterVerified === 'verified';
            if (filterSuspended !== 'all') params.is_suspended = filterSuspended === 'suspended';

            const response = await api.get('/admin/helpers', { params });
            setHelpers(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch helpers:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleVerify = async (helperId) => {
        if (!confirm('Verify this helper?')) return;
        try {
            await api.put(`/admin/helpers/${helperId}/verify`);
            alert('Helper verified successfully');
            fetchHelpers();
        } catch (error) {
            alert('Failed to verify helper');
        }
    };

    const handleSuspend = async (helperId, shouldSuspend = true) => {
        if (!confirm(shouldSuspend ? 'Suspend this helper?' : 'Unsuspend this helper?')) return;
        try {
            await api.put(`/admin/helpers/${helperId}/suspend`, { suspend: shouldSuspend });
            alert(shouldSuspend ? 'Helper suspended' : 'Helper unsuspended');
            fetchHelpers();
        } catch (error) {
            alert('Action failed');
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 pb-12">
            <header className="bg-white shadow-sm border-b">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button onClick={() => navigate(-1)} className="flex items-center gap-2 text-slate-600 hover:text-primary">
                        <ArrowLeft size={20} />
                        <span className="font-medium">Back</span>
                    </button>
                    <div className="flex items-center gap-2">
                        <Users className="text-green-600" size={24} />
                        <h1 className="text-xl font-bold text-slate-900">Helper Management</h1>
                    </div>
                    <div className="w-16"></div>
                </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Search & Filter */}
                <div className="bg-white rounded-xl p-6 shadow-sm border mb-6">
                    <div className="flex flex-col md:flex-row gap-4">
                        <div className="flex-1 relative">
                            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" size={20} />
                            <input
                                type="text"
                                placeholder="Search by name, email, or skills..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div className="flex gap-4">
                            <select value={filterVerified} onChange={(e) => setFilterVerified(e.target.value)}
                                className="px-4 py-2 border rounded-lg">
                                <option value="all">All Helpers</option>
                                <option value="verified">Verified</option>
                                <option value="unverified">Unverified</option>
                            </select>
                            <select value={filterSuspended} onChange={(e) => setFilterSuspended(e.target.value)}
                                className="px-4 py-2 border rounded-lg">
                                <option value="all">All Status</option>
                                <option value="active">Active Only</option>
                                <option value="suspended">Suspended Only</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Table */}
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
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Rating</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Status</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y">
                                    {helpers.length === 0 ? (
                                        <tr>
                                            <td colSpan="6" className="px-6 py-12 text-center text-slate-500">No helpers found</td>
                                        </tr>
                                    ) : (
                                        helpers.map((helper) => (
                                            <tr key={helper.id} className="hover:bg-slate-50">
                                                <td className="px-6 py-4 text-sm">{helper.id}</td>
                                                <td className="px-6 py-4 text-sm font-medium">{helper.name}</td>
                                                <td className="px-6 py-4 text-sm text-slate-600">{helper.email}</td>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-1">
                                                        <Star className="text-yellow-500" size={16} fill="currentColor" />
                                                        <span className="text-sm font-medium">{helper.rating || '5.0'}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex gap-2">
                                                        <span className={`px-2 py-1 text-xs font-semibold rounded-full ${helper.is_verified ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
                                                            {helper.is_verified ? 'Verified' : 'Unverified'}
                                                        </span>
                                                        {helper.is_suspended && (
                                                            <span className="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Suspended</span>
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex gap-2">
                                                        {!helper.is_verified && (
                                                            <button onClick={() => handleVerify(helper.id)}
                                                                className="flex items-center gap-1 px-3 py-1 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 text-xs font-medium">
                                                                <CheckCircle size={14} />
                                                                Verify
                                                            </button>
                                                        )}
                                                        {helper.is_suspended ? (
                                                            <button onClick={() => handleSuspend(helper.id, false)}
                                                                className="flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 text-xs font-medium">
                                                                <Shield size={14} />
                                                                Unsuspend
                                                            </button>
                                                        ) : (
                                                            <button onClick={() => handleSuspend(helper.id, true)}
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

export default HelperManagement;

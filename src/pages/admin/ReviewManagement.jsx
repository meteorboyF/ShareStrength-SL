import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import { ArrowLeft, Star, Trash2, Search } from 'lucide-react';

const ReviewManagement = () => {
    const navigate = useNavigate();
    const [reviews, setReviews] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [filterRating, setFilterRating] = useState('all');

    useEffect(() => {
        fetchReviews();
    }, [searchTerm, filterRating]);

    const fetchReviews = async () => {
        try {
            const params = {};
            if (searchTerm) params.search = searchTerm;
            if (filterRating !== 'all') params.rating = filterRating;
            const response = await api.get('/admin/reviews', { params });
            setReviews(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch reviews:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (reviewId) => {
        if (!confirm('Delete this review? This action cannot be undone.')) return;
        try {
            await api.delete(`/admin/reviews/${reviewId}`);
            alert('Review deleted successfully');
            fetchReviews();
        } catch (error) {
            alert('Failed to delete review');
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
                        <Star className="text-yellow-500" size={24} />
                        <h1 className="text-xl font-bold">Review Management</h1>
                    </div>
                    <div className="w-16"></div>
                </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div className="bg-white rounded-xl p-6 shadow-sm border mb-6">
                    <div className="flex gap-4">
                        <div className="flex-1 relative">
                            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" size={20} />
                            <input type="text" placeholder="Search comments..." value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="w-full pl-10 pr-4 py-2 border rounded-lg" />
                        </div>
                        <select value={filterRating} onChange={(e) => setFilterRating(e.target.value)}
                            className="px-4 py-2 border rounded-lg">
                            <option value="all">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
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
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Reviewer</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Helper</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Rating</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Comment</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {reviews.length === 0 ? (
                                    <tr><td colSpan="7" className="px-6 py-12 text-center text-slate-500">No reviews found</td></tr>
                                ) : (
                                    reviews.map((review) => (
                                        <tr key={review.id} className="hover:bg-slate-50">
                                            <td className="px-6 py-4 text-sm">{review.id}</td>
                                            <td className="px-6 py-4 text-sm">{review.task?.title || 'N/A'}</td>
                                            <td className="px-6 py-4 text-sm">{review.reviewer?.name || 'N/A'}</td>
                                            <td className="px-6 py-4 text-sm">{review.reviewee?.name || 'N/A'}</td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-1">
                                                    <Star className="text-yellow-500" size={16} fill="currentColor" />
                                                    <span className="font-medium">{review.rating}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">{review.comment || 'No comment'}</td>
                                            <td className="px-6 py-4">
                                                <button onClick={() => handleDelete(review.id)}
                                                    className="flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 text-xs font-medium">
                                                    <Trash2 size={14} />
                                                    Delete
                                                </button>
                                            </td>
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

export default ReviewManagement;

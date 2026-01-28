import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';

const FindHelper = () => {
    const [helpers, setHelpers] = useState([]);
    const [search, setSearch] = useState('');
    const [skillFilter, setSkillFilter] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchHelpers();
    }, [search, skillFilter]); // Auto-refresh on type (debouncing recommended normally but keep simple)

    const fetchHelpers = async () => {
        setLoading(true);
        try {
            const params = {};
            if (search) params.search = search;
            if (skillFilter) params.skill = skillFilter;

            const response = await api.get('/helpers', { params });
            setHelpers(response.data);
        } catch (error) {
            console.error('Failed to fetch helpers:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-neutral-light font-sans text-neutral-dark pb-12">
            {/* Header */}
            <header className="bg-white shadow-sm sticky top-0 z-40">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <Link to="/dashboard" className="text-primary hover:text-primary-dark font-medium flex items-center gap-1">
                        &larr; Back
                    </Link>
                    <h1 className="text-xl font-bold text-neutral-darkest">Find a HelpMate</h1>
                    <div className="w-16"></div>
                </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">

                {/* Search & Filter */}
                <div className="bg-white p-6 rounded-xl shadow-sm border border-neutral-100 mb-8 flex flex-col md:flex-row gap-4">
                    <div className="flex-1">
                        <label className="block text-xs font-bold text-neutral-500 uppercase mb-1">Search</label>
                        <div className="relative">
                            <input
                                type="text"
                                placeholder="Search by name, email, or keywords..."
                                className="w-full border border-neutral-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                            <svg className="w-5 h-5 text-neutral-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                    <div className="flex-1">
                        <label className="block text-xs font-bold text-neutral-500 uppercase mb-1">Filter by Skill</label>
                        <div className="relative">
                            <input
                                type="text"
                                placeholder="e.g. Nursing, Driving, Cooking..."
                                className="w-full border border-neutral-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                value={skillFilter}
                                onChange={(e) => setSkillFilter(e.target.value)}
                            />
                            <svg className="w-5 h-5 text-neutral-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        </div>
                    </div>
                </div>

                {/* Grid */}
                {loading ? (
                    <div className="text-center py-12">
                        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
                        <p className="text-neutral-500">Finding matches...</p>
                    </div>
                ) : helpers.length === 0 ? (
                    <div className="text-center py-12 bg-white rounded-xl border border-dashed border-neutral-300">
                        <p className="text-neutral-500">No helpers found matching your criteria.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {helpers.map(helper => (
                            <div key={helper.id} className="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden hover:shadow-md transition flex flex-col">
                                <div className="p-6 flex-1">
                                    <div className="flex items-start justify-between mb-4">
                                        <div className="flex items-center gap-3">
                                            <img src={helper.profile_photo || 'https://placehold.co/150'} alt={helper.name} className="w-12 h-12 rounded-full object-cover border border-neutral-100" />
                                            <div>
                                                <h3 className="font-bold text-neutral-darkest">{helper.name}</h3>
                                                <p className="text-xs text-neutral-500">{helper.location || 'Remote'}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded text-yellow-700 text-xs font-bold">
                                            <span>★</span>
                                            <span>{helper.rating || 'New'}</span>
                                        </div>
                                    </div>

                                    <div className="mb-4">
                                        <p className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Skills</p>
                                        <div className="flex flex-wrap gap-1">
                                            {helper.skills ? helper.skills.split(',').slice(0, 4).map((skill, i) => (
                                                <span key={i} className="bg-neutral-100 text-neutral-600 text-xs px-2 py-1 rounded-md">{skill.trim()}</span>
                                            )) : <span className="text-xs text-neutral-400">No skills listed</span>}
                                        </div>
                                    </div>
                                </div>
                                <div className="p-4 bg-neutral-50 border-t border-neutral-100 grid grid-cols-2 gap-3">
                                    <Link to={`/profile/helpmate/${helper.id}`} className="bg-white border border-neutral-200 text-neutral-700 text-center py-2 rounded-lg text-sm font-semibold hover:bg-neutral-100 transition">
                                        View Profile
                                    </Link>
                                    <Link to={`/post-task?helper=${helper.id}`} className="bg-primary text-white text-center py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition">
                                        Hire
                                    </Link>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </main>
        </div>
    );
};

export default FindHelper;

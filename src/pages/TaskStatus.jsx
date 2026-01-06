import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';

const TaskStatus = () => {
    const [applications, setApplications] = useState([]);
    const [selectedTask, setSelectedTask] = useState(null);

    useEffect(() => {
        fetchApplications();
    }, []);

    const fetchApplications = async () => {
        try {
            const res = await api.get('/applications');
            const mapped = res.data.map(app => ({
                id: app.id,
                title: app.task?.title || 'Unknown Task',
                user_name: app.task?.creator?.name || 'Unknown User',
                description: app.task?.description || 'No description',
                location: app.task?.location || 'Remote',
                skill: app.task?.required_skills?.[0] || 'General',
                rate: `$${app.task?.budget || 0} /hr`,
                status: app.status,
                date: app.created_at ? new Date(app.created_at).toLocaleDateString() : 'Recently'
            }));
            setApplications(mapped);
        } catch (err) {
            console.error("Failed to fetch applications", err);
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'accepted': return 'bg-green-100 text-green-800 border-green-200';
            case 'pending': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
            case 'rejected': return 'bg-red-100 text-red-800 border-red-200';
            default: return 'bg-gray-100 text-gray-800 border-gray-200';
        }
    };

    return (
        <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-8">
            <div className="max-w-5xl mx-auto">

                {/* Header */}
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <div>
                        <h1 className="text-3xl font-extrabold text-neutral-darkest">My Applications</h1>
                        <p className="text-neutral-medium mt-1">Track the status of your job applications.</p>
                    </div>
                    <Link to="/helpmate-dashboard" className="text-sm font-bold text-green-600 hover:underline">
                        &larr; Back to Dashboard
                    </Link>
                </div>

                {/* Task Grid */}
                <div className="grid gap-6">
                    {applications.length === 0 ? (
                        <div className="text-center py-20 bg-white rounded-xl shadow-sm">
                            <p className="text-neutral-medium">You haven't applied to any tasks yet.</p>
                            <Link to="/helpmate-dashboard" className="mt-4 inline-block text-green-600 font-bold">Browse Jobs</Link>
                        </div>
                    ) : (
                        applications.map((task) => (
                            <div key={task.id} className="bg-white rounded-xl p-6 shadow-sm border border-neutral-200 flex flex-col md:flex-row gap-6 hover:shadow-md transition">

                                {/* Status Column */}
                                <div className={`md:w-2 rounded-full ${getStatusColor(task.status).split(' ')[0].replace('bg-', 'bg-')}`}></div>

                                {/* Main Content */}
                                <div className="flex-grow">
                                    <div className="flex justify-between items-start mb-2">
                                        <h3 className="text-xl font-bold text-neutral-darkest">{task.title}</h3>
                                        <span className={`px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border ${getStatusColor(task.status)}`}>
                                            {task.status}
                                        </span>
                                    </div>

                                    <p className="text-sm text-green-700 font-semibold mb-3">Posted by {task.user_name}</p>
                                    <p className="text-neutral-medium text-sm leading-relaxed mb-4">{task.description}</p>

                                    <div className="flex flex-wrap gap-4 text-sm text-neutral-500">
                                        <div className="flex items-center gap-1">
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                            {task.skill}
                                        </div>
                                        <div className="flex items-center gap-1">
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {task.rate}
                                        </div>
                                        <div className="flex items-center gap-1">
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Applied on {task.date}
                                        </div>
                                    </div>
                                </div>
                                <div className="flex md:flex-col justify-end gap-2 md:w-44">
                                    {/* Simplified actions for now */}
                                    <button
                                        onClick={() => setSelectedTask(task)}
                                        className="w-full border border-neutral-300 text-neutral-dark font-semibold py-2 px-4 rounded-lg hover:bg-neutral-50 transition text-sm">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        ))
                    )}
                </div>

                {/* Details Modal */}
                {selectedTask && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in-up">
                        <div className="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
                            <h3 className="text-xl font-bold text-slate-900 mb-2">{selectedTask.title}</h3>
                            <div className="space-y-3">
                                <div>
                                    <span className="text-xs font-bold text-slate-500 uppercase">Posted By</span>
                                    <p className="text-slate-800">{selectedTask.user_name}</p>
                                </div>
                                <div>
                                    <span className="text-xs font-bold text-slate-500 uppercase">Full Description</span>
                                    <p className="text-slate-600 text-sm leading-relaxed">{selectedTask.description}</p>
                                </div>
                                <div>
                                    <span className="text-xs font-bold text-slate-500 uppercase">Location</span>
                                    <p className="text-slate-800">{selectedTask.location}</p>
                                </div>
                                <div className="flex justify-between items-center bg-slate-50 p-3 rounded-lg border border-slate-100 mt-2">
                                    <span className="font-bold text-green-700">{selectedTask.rate}</span>
                                    <span className={`text-xs font-bold px-2 py-1 rounded full uppercase ${getStatusColor(selectedTask.status)}`}>{selectedTask.status}</span>
                                </div>
                            </div>
                            <div className="mt-6 flex justify-end">
                                <button
                                    onClick={() => setSelectedTask(null)}
                                    className="px-4 py-2 bg-slate-200 text-slate-800 font-bold rounded-lg hover:bg-slate-300 transition"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                )}

            </div>
        </div>
    );
};

export default TaskStatus;
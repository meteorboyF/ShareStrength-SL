import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import authService from '../services/authService';
import { X } from 'lucide-react';

const HelpMateDashboard = () => {
    // State
    const [availableTasks, setAvailableTasks] = useState([]);
    const [appliedJobs, setAppliedJobs] = useState([]);
    const [offers, setOffers] = useState([]);
    const [activeJobs, setActiveJobs] = useState([]);
    const [payments, setPayments] = useState([]);
    const [user, setUser] = useState({ name: 'HelpMate', email: '', photo: 'https://placehold.co/150x150', skills: [] });

    // Modal State
    const [showModal, setShowModal] = useState(false);
    const [selectedTask, setSelectedTask] = useState(null);

    useEffect(() => {
        const fetchCurrentUser = async () => {
            try {
                // Fetch fresh user data from backend to ensure correct user is displayed
                const response = await api.get('/me');
                const currentUser = response.data;
                setUser({ ...currentUser, skills: currentUser.skills ? currentUser.skills.split(', ') : [] });

                // Update localStorage with fresh data
                localStorage.setItem('user', JSON.stringify(currentUser));

                loadDashboardData();
                fetchPayments();
            } catch (error) {
                console.error('Failed to fetch current user:', error);
                // Fallback to localStorage if API call fails
                const cachedUser = authService.getCurrentUser();
                if (cachedUser) {
                    setUser({ ...cachedUser, skills: cachedUser.skills ? cachedUser.skills.split(', ') : [] });
                    loadDashboardData();
                    fetchPayments();
                }
            }
        };

        fetchCurrentUser();

        // Listen for storage changes from other tabs
        const handleStorageChange = (e) => {
            // If token changed in another tab, reload to get correct user
            if (e.key === 'token' && e.oldValue !== e.newValue) {
                console.log('Token changed in another tab, reloading...');
                window.location.reload();
            }
        };

        window.addEventListener('storage', handleStorageChange);
        return () => window.removeEventListener('storage', handleStorageChange);
    }, []);

    const loadDashboardData = async () => {
        try {
            // Fetch everything in parallel
            const [tasksRes, appsRes] = await Promise.all([
                api.get('/tasks'),
                api.get('/applications')
            ]);

            const allTasks = tasksRes.data;
            const myApps = appsRes.data;

            // 1. Set Applied Jobs
            // Map backend application to UI format
            const formattedApps = myApps.map(app => ({
                id: app.id,
                task_id: app.task_id,
                title: app.task?.title || 'Unknown Task',
                user_name: app.task?.creator?.name || 'Unknown User', // Assuming task.creator is loaded
                status: app.status
            }));
            setAppliedJobs(formattedApps);

            // 2. Filter Available Tasks
            const appliedTaskIds = new Set(myApps.map(app => app.task_id));
            const currentUser = authService.getCurrentUser();
            const currentUserId = currentUser?.id; // Standardize on ID

            const filteredTasks = allTasks.filter(t =>
                t.status === 'open' && // ONLY show open tasks
                !appliedTaskIds.has(t.task_id || t.id) &&
                // Show if I am a caregiver (can see all open tasks) OR if I am a user seeing other's tasks (marketplace logic)
                // But specifically for Helper Dashboard, we want to see everything I can apply to.
                (currentUser.role === 'helpmate' || currentUser.account_type === 'helpmate' ? true : t.created_by !== currentUserId)
            )
                .sort((a, b) => {
                    // Sort by created_at descending (newest first)
                    const dateA = new Date(a.created_at);
                    const dateB = new Date(b.created_at);
                    return dateB - dateA;
                })
                .map(t => ({
                    ...t,
                    id: t.task_id || t.id,
                    user_id: t.created_by || t.creator?.id,
                    user_name: t.creator?.name || 'Unknown User',
                    user_photo: t.creator?.profile_photo || 'https://placehold.co/150',
                    skill: t.skill_required, // Map skill_required to skill
                }));
            setAvailableTasks(filteredTasks);

            // 3. Set Active Jobs (Assigned to me)
            const myActiveJobs = allTasks.filter(t =>
                (t.status === 'accepted' || t.status === 'in_progress' || t.status === 'paused') &&
                (t.caregiver_id == currentUserId)
            );

            // 4. Set Pending Offers (Assigned but not accepted yet)
            const myOffers = allTasks.filter(t =>
                t.status === 'requested' && t.caregiver_id == currentUserId
            );

            // Map offers to UI format
            setOffers(myOffers.map(t => ({
                id: t.id,
                title: t.title,
                user_name: t.creator?.name || 'User',
                hourly_rate: t.budget || t.hourly_rate || '0.00',
                status: t.status
            })));

            // Map Active Jobs to UI format
            setActiveJobs(myActiveJobs.map(t => ({
                id: t.task_id || t.id,
                title: t.title,
                user_id: t.creator?.id || t.created_by,
                user_name: t.creator?.name || 'User',
                start_time: t.started_at ? new Date(t.started_at).getTime() : null,
                accumulated_seconds: t.accumulated_seconds || 0,
                status: t.status
            })));

        } catch (error) {
            console.error("Error loading dashboard data:", error);
        }
    };

    const fetchPayments = async () => {
        try {
            const res = await api.get('/payments');
            console.log('Helper payments:', res.data);
            setPayments(res.data);
        } catch (error) {
            console.error("Error loading payments:", error);
        }
    };

    const handleStartTask = async (taskId) => {
        try {
            await api.put(`/tasks/${taskId}/start`);
            loadDashboardData();
        } catch (e) {
            console.error(e);
            alert("Failed to start task: " + (e.response?.data?.message || e.message));
        }
    };

    const handlePauseTask = async (taskId) => {
        try {
            await api.put(`/tasks/${taskId}/pause`);
            loadDashboardData();
        } catch (e) {
            console.error(e);
            alert("Failed to pause task: " + (e.response?.data?.message || e.message));
        }
    };

    const handleResumeTask = async (taskId) => {
        try {
            await api.put(`/tasks/${taskId}/resume`);
            loadDashboardData();
        } catch (e) {
            console.error(e);
            alert("Failed to resume task: " + (e.response?.data?.message || e.message));
        }
    };

    const handleEndTask = async (taskId) => {
        try {
            await api.put(`/tasks/${taskId}/complete`);
            alert("Task Completed! Payment calculated.");
            loadDashboardData();
        } catch (e) {
            console.error(e);
            alert("Failed to complete task: " + (e.response?.data?.message || e.message));
        }
    };

    const handleOfferAction = async (id, action) => {
        if (action === 'accept') {
            try {
                await api.put(`/tasks/${id}/accept`);
                alert("Offer Accepted!");
                loadDashboardData();
            } catch (e) {
                console.error(e);
                alert("Failed to accept offer: " + (e.response?.data?.message || e.message));
            }
        } else {
            // Reject logic (for now just client side remove or api call if implemented)
            setOffers(offers.filter(o => o.id !== id));
            alert("Offer Rejected");
        }
    };

    const fetchTasks = async () => {
        // Deprecated
    };

    // --- Live Timer Component ---
    const Timer = ({ startTime, accumulatedSeconds, isRunning }) => {
        const [elapsedString, setElapsedString] = useState("00:00:00");

        useEffect(() => {
            const calculateTime = () => {
                let totalSeconds = accumulatedSeconds || 0;

                if (isRunning && startTime) {
                    const now = Date.now();
                    // Max with 0 to prevent negative flashes if clocks drift slightly
                    const currentSessionSeconds = Math.max(0, Math.floor((now - startTime) / 1000));
                    totalSeconds += currentSessionSeconds;
                }

                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            };

            setElapsedString(calculateTime());

            if (!isRunning) return;

            const interval = setInterval(() => {
                setElapsedString(calculateTime());
            }, 1000);

            return () => clearInterval(interval);
        }, [startTime, accumulatedSeconds, isRunning]);

        return <span className="font-mono text-sm text-slate-700">{elapsedString}</span>;
    };

    // Handlers
    const handleApplyClick = (task) => {
        setSelectedTask(task);
        setShowModal(true);
    };

    const confirmApply = async () => {
        if (!selectedTask) return;

        try {
            await api.post('/applications', { task_id: selectedTask.id });

            // Remove from available immediately for responsiveness
            setAvailableTasks(availableTasks.filter(t => t.id !== selectedTask.id));

            alert("Application submitted!");

            // Reload to ensure sync
            loadDashboardData();
        } catch (err) {
            alert("Failed to apply: " + (err.response?.data?.message || err.message));
        }

        setShowModal(false);
        setSelectedTask(null);
    };

    return (
        <div className="min-h-screen bg-slate-50 font-sans text-slate-900">

            {/* Header */}
            <header className="bg-white shadow-sm sticky top-0 z-40 border-b border-green-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-slate-900">Welcome, {user.name}!</h1>
                        <p className="text-xs text-slate-500">Manage your jobs and find new tasks.</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <button className="hidden sm:inline-flex items-center gap-x-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 border border-slate-200 transition">
                            Edit Profile
                        </button>
                        <Link to="/my-earnings" className="inline-flex items-center gap-x-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 border border-slate-200 transition">
                            My Earnings
                        </Link>
                        <Link to="/messages" className="inline-flex items-center gap-x-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 border border-slate-200 transition">
                            Messages
                        </Link>
                        <Link to="/" className="inline-flex items-center gap-x-2 rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                            Log Out
                        </Link>
                    </div>
                </div>
            </header>

            <div className="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {/* --- SIDEBAR --- */}
                    <aside className="lg:col-span-1 space-y-6">

                        {/* Profile Card */}
                        <section className="bg-green-700 text-white p-6 rounded-xl shadow-lg animate-fade-in-up">
                            <div className="flex items-center gap-4">
                                <img className="h-16 w-16 rounded-full object-cover border-2 border-green-400" src={user.photo || 'https://placehold.co/150'} alt="Profile" />
                                <div>
                                    <h3 className="font-bold text-lg">{user.name}</h3>
                                    <p className="text-sm text-green-100">{user.email}</p>
                                </div>
                            </div>
                            <div className="mt-4 flex items-center justify-between text-sm">
                                <span className="font-semibold text-green-200">Rating:</span>
                                <span className="font-bold text-yellow-300 flex items-center gap-1">
                                    ★ {user.rating || 'New'}
                                </span>
                            </div>
                            <div className="mt-4 pt-4 border-t border-green-600">
                                <h4 className="font-semibold text-sm text-green-200 mb-2">My Skills</h4>
                                <div className="flex flex-wrap gap-2">
                                    {user.skills && user.skills.length > 0 ? user.skills.map(skill => (
                                        <span key={skill} className="bg-green-600 text-white text-xs font-medium px-2 py-1 rounded-full border border-green-500">{skill}</span>
                                    )) : <span className="text-xs text-green-300">No skills listed</span>}
                                </div>
                            </div>
                        </section>



                        {/* Applied Jobs List */}
                        <section className="bg-white p-4 rounded-lg border border-slate-200 shadow-sm animate-fade-in-up" style={{ animationDelay: '200ms' }}>
                            <h4 className="font-semibold text-sm mb-3 text-slate-600">Applied Jobs</h4>
                            <ul className="space-y-2">
                                {appliedJobs.length === 0 ? (
                                    <li className="text-xs text-slate-400">No active applications.</li>
                                ) : (
                                    appliedJobs.map(job => (
                                        <li key={job.id} className="text-sm p-2 bg-slate-50 rounded-md border border-slate-100">
                                            <div className="font-medium text-slate-700">{job.title}</div>
                                            <div className="text-xs text-slate-500">Posted by {job.user_name}</div>
                                        </li>
                                    ))
                                )}
                            </ul>
                            {/* View All Link */}
                            <div className="mt-4 pt-3 border-t border-slate-100 text-center">
                                <Link to="/task-status" className="text-xs font-bold text-green-600 hover:text-green-700 hover:underline">
                                    View All Applications &rarr;
                                </Link>
                            </div>
                        </section>
                    </aside>

                    {/* --- MAIN CONTENT --- */}
                    <main className="lg:col-span-2 space-y-6">

                        {/* 1. Job Offers */}
                        <section className="animate-fade-in-up">
                            <h2 className="text-lg font-semibold text-slate-800 mb-3">New Job Offers</h2>
                            {offers.length === 0 ? (
                                <div className="text-center bg-white p-6 rounded-lg border border-dashed border-slate-300 text-slate-500 text-sm">
                                    No pending offers.
                                </div>
                            ) : (
                                offers.map(offer => (
                                    <div key={offer.id} className="bg-white p-5 rounded-lg border-2 border-green-500 shadow-lg mb-4">
                                        <p className="text-sm text-slate-600"><strong className="text-green-700">{offer.user_name}</strong> sent you an offer:</p>
                                        <h3 className="font-bold text-lg text-slate-900 mt-1">{offer.title}</h3>
                                        <div className="mt-4 flex items-center justify-between">
                                            <p className="text-lg font-bold text-slate-800">${offer.hourly_rate}<span className="font-normal text-sm text-slate-500">/hr</span></p>
                                            <div className="flex gap-2">
                                                <button onClick={() => handleOfferAction(offer.id, 'reject')} className="text-xs font-semibold bg-red-100 text-red-700 px-3 py-1.5 rounded-full hover:bg-red-200 transition">Reject</button>
                                                <button onClick={() => handleOfferAction(offer.id, 'accept')} className="text-sm font-semibold bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm">Accept Offer</button>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </section>

                        {/* 2. Active Jobs (With Timer) */}
                        <section className="animate-fade-in-up" style={{ animationDelay: '100ms' }}>
                            <h2 className="text-lg font-semibold text-slate-800 mb-3">Active Jobs</h2>
                            <div className="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                                {activeJobs.length === 0 ? (
                                    <p className="text-sm text-slate-400 text-center py-4">No jobs currently in progress.</p>
                                ) : (
                                    <ul className="space-y-4">
                                        {activeJobs.map(job => (
                                            <li key={job.id} className="text-sm p-3 bg-blue-50 border border-blue-200 rounded-md">
                                                <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                                    <div>
                                                        <p className="font-bold text-blue-800">{job.title}</p>
                                                        <p className="text-xs text-blue-600">for {job.user_name} ({job.status})</p>
                                                        {job.user_id && (
                                                            <Link
                                                                to={`/user/${job.user_id}`}
                                                                className="text-xs font-semibold text-blue-700 hover:text-blue-900 underline mt-1.5 flex items-center gap-1"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                                                                View Profile & Trusted Contacts
                                                            </Link>
                                                        )}
                                                    </div>
                                                    <div className="flex items-center gap-2">
                                                        {job.status === 'accepted' && (
                                                            <button
                                                                onClick={() => handleStartTask(job.id)}
                                                                className="text-xs font-bold bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600 transition"
                                                            >
                                                                Start Task
                                                            </button>
                                                        )}

                                                        {(job.status === 'in_progress' || job.status === 'paused') && (
                                                            <>
                                                                <div className="bg-white px-3 py-1 rounded border border-blue-100 shadow-sm">
                                                                    <Timer
                                                                        startTime={job.start_time}
                                                                        accumulatedSeconds={job.accumulated_seconds}
                                                                        isRunning={job.status === 'in_progress'}
                                                                    />
                                                                </div>

                                                                {job.status === 'in_progress' ? (
                                                                    <button
                                                                        onClick={() => handlePauseTask(job.id)}
                                                                        className="text-xs font-bold bg-amber-500 text-white px-3 py-2 rounded hover:bg-amber-600 transition"
                                                                    >
                                                                        Pause
                                                                    </button>
                                                                ) : (
                                                                    <button
                                                                        onClick={() => handleResumeTask(job.id)}
                                                                        className="text-xs font-bold bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition"
                                                                    >
                                                                        Resume
                                                                    </button>
                                                                )}

                                                                <button
                                                                    onClick={() => handleEndTask(job.id)}
                                                                    className="text-xs font-bold bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition"
                                                                >
                                                                    End
                                                                </button>
                                                            </>
                                                        )}
                                                    </div>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>
                        </section>

                        {/* 3. Available Tasks Feed */}
                        <section className="animate-fade-in-up" style={{ animationDelay: '200ms' }}>
                            <h2 className="text-lg font-semibold text-slate-800 mb-3">Available Tasks For You</h2>
                            <div className="space-y-4 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                                {availableTasks.length === 0 ? (
                                    <div className="text-center bg-white p-12 rounded-lg border border-dashed border-slate-300">
                                        <h3 className="mt-2 text-sm font-semibold text-slate-900">No Available Tasks</h3>
                                        <p className="mt-1 text-sm text-slate-500">Check back later.</p>
                                    </div>
                                ) : (
                                    availableTasks.map(task => (
                                        <div key={task.id} className="bg-white p-5 rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition">
                                            <div className="flex justify-between items-start">
                                                <div className="flex gap-3">
                                                    <img src={task.user_photo} alt={task.user_name} className="h-10 w-10 rounded-full" />
                                                    <div>
                                                        <h3 className="font-bold text-green-700">{task.title}</h3>
                                                        <p className="text-xs text-slate-500">
                                                            Posted by{' '}
                                                            {task.user_id ? (
                                                                <Link to={`/user/${task.user_id}`} className="text-green-600 hover:underline font-medium">
                                                                    {task.user_name}
                                                                </Link>
                                                            ) : (
                                                                <span>{task.user_name}</span>
                                                            )}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-xl font-bold text-slate-900">${task.budget || task.hourly_rate || '0.00'}</p>
                                                    <p className="text-xs text-slate-500">/hr</p>
                                                </div>
                                            </div>
                                            <p className="text-sm text-slate-600 mt-3 mb-4">{task.description}</p>
                                            <div className="flex justify-between items-center border-t border-slate-100 pt-4">
                                                <div className="flex gap-2">
                                                    <span className="bg-slate-100 text-slate-600 text-xs font-semibold px-2 py-1 rounded-full">{task.skill}</span>
                                                    <span className={`text-xs font-semibold px-2 py-1 rounded-full ${task.urgency === 'High' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`}>{task.urgency} Priority</span>
                                                </div>
                                                <button
                                                    onClick={() => handleApplyClick(task)}
                                                    className="text-sm font-semibold bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm"
                                                >
                                                    Apply Now
                                                </button>
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                        </section>
                    </main>
                </div>
            </div>

            {/* Confirmation Modal */}
            {showModal && selectedTask && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in-up">
                    <div className="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md relative">
                        <button
                            onClick={() => setShowModal(false)}
                            className="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition"
                        >
                            <X size={20} />
                        </button>
                        <h3 className="text-lg font-bold text-slate-900">Confirm Application</h3>
                        <p className="mt-2 text-sm text-slate-600">
                            Do you want to apply for <strong className="text-green-700">{selectedTask.title}</strong>?
                        </p>
                        <div className="mt-6 flex justify-end gap-3">
                            <button
                                onClick={() => setShowModal(false)}
                                className="px-4 py-2 text-sm font-semibold bg-slate-200 text-slate-800 rounded-md hover:bg-slate-300 transition"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={confirmApply}
                                className="px-4 py-2 text-sm font-semibold bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                            >
                                Yes, Apply Now
                            </button>
                        </div>
                    </div>
                </div>
            )}

        </div>
    );
};

export default HelpMateDashboard;
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import Chatbot from '../components/Chatbot';
import { useCart } from '../context/CartContext';
import authService from '../services/authService';
import api from '../services/api';

const TASKS_WITH_APPLICANTS = [];
const PENDING_PAYMENTS = [];

const UserDashboard = () => {
    const { cartCount } = useCart();
    const [openApplicantTask, setOpenApplicantTask] = useState(null);
    const [showBanner, setShowBanner] = useState(true);
    const [tasks, setTasks] = useState([]);
    const [user, setUser] = useState({ name: 'User', profile_photo: 'https://placehold.co/100x100' });
    const [tasksWithApplicants, setTasksWithApplicants] = useState([]);
    const [showCompleted, setShowCompleted] = useState(false);

    useEffect(() => {
        const fetchCurrentUser = async () => {
            try {
                // Fetch fresh user data from backend to ensure correct user is displayed
                const response = await api.get('/me');
                const currentUser = response.data;
                setUser(currentUser);

                // Update localStorage with fresh data
                localStorage.setItem('user', JSON.stringify(currentUser));

                fetchTasks();
                fetchApplications();
            } catch (error) {
                console.error('Failed to fetch current user:', error);
                // Fallback to localStorage if API call fails
                const cachedUser = authService.getCurrentUser();
                if (cachedUser) {
                    setUser(cachedUser);
                    fetchTasks();
                    fetchApplications();
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

    const fetchTasks = async () => {
        try {
            const response = await api.get('/my-tasks');
            const tasksData = response.data.data || response.data;
            console.log('Fetched tasks:', tasksData);
            console.log('In-progress tasks:', tasksData.filter(t => t.status === 'in_progress'));
            setTasks(tasksData);
        } catch (err) {
            console.error(err);
        }
    };

    const fetchApplications = async () => {
        try {
            const res = await api.get('/applications/received');
            // Group by Task
            const grouped = {};
            res.data.forEach(app => {
                // Skip rejected applications if user wants them "deleted" from view
                if (app.status === 'rejected') return;

                const tId = app.task_id;
                if (!grouped[tId]) {
                    grouped[tId] = {
                        id: tId,
                        title: app.task?.title || 'Unknown Task',
                        applicant_count: 0,
                        applicants: []
                    };
                }
                grouped[tId].applicants.push({
                    id: app.helper?.id,
                    application_id: app.id,
                    name: app.helper?.name || 'Helper',
                    photo: app.helper?.profile_photo_url || 'https://placehold.co/150',
                    rating: 'New', // mock unless helper has rating
                    status: app.status
                });
                grouped[tId].applicant_count++;
            });
            setTasksWithApplicants(Object.values(grouped));
        } catch (err) {
            console.error("Failed to fetch applications", err);
        }
    };

    const handleApplicationAction = async (taskId, applicationId, status) => {
        try {
            await api.put(`/applications/${applicationId}`, { status });
            alert(`Applicant ${status === 'accepted' ? 'hired' : 'rejected'} successfully!`);
            fetchApplications(); // Refresh list
        } catch (err) {
            console.error(`Failed to ${status} applicant`, err);
            alert(`Failed to ${status} applicant. ` + (err.response?.data?.message || err.message));
        }
    };

    const handleDeleteTask = async (taskId) => {
        if (!window.confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
            return;
        }
        try {
            await api.delete(`/tasks/${taskId}`);
            alert('Task deleted successfully!');
            fetchTasks();
        } catch (err) {
            console.error('Failed to delete task', err);
            alert('Failed to delete task. ' + (err.response?.data?.message || ''));
        }
    };

    const handleRepostTask = async (taskId) => {
        try {
            await api.post(`/tasks/${taskId}/repost`);
            alert('Task reposted successfully!');
            fetchTasks();
        } catch (err) {
            console.error('Failed to repost task', err);
            alert('Failed to repost task. ' + (err.response?.data?.message || ''));
        }
    };

    // --- Live Timer Component ---
    const Timer = ({ startTime }) => {
        const [elapsed, setElapsed] = useState("00:00:00");

        useEffect(() => {
            // Validate startTime
            if (!startTime || typeof startTime !== 'number' || startTime <= 0) {
                setElapsed("00:00:00");
                return;
            }

            const interval = setInterval(() => {
                const now = Date.now();
                const diff = now - startTime;

                // If diff is negative, show 00:00:00
                if (diff < 0) {
                    setElapsed("00:00:00");
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                setElapsed(
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                );
            }, 1000);
            return () => clearInterval(interval);
        }, [startTime]);

        return <span className="font-mono text-sm text-blue-700 font-semibold">{elapsed}</span>;
    };

    const toggleApplicants = (taskId) => {
        setOpenApplicantTask(openApplicantTask === taskId ? null : taskId);
    };

    return (
        <div className="min-h-screen bg-neutral-light font-sans text-neutral-dark">

            {/* --- Header --- */}
            <header className="bg-white shadow-sm sticky top-0 z-40">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div className="flex items-center gap-4">
                        <img src={user.profile_photo || "https://placehold.co/100x100"} alt="Profile" className="h-12 w-12 rounded-full border-2 border-primary object-cover" />
                        <div>
                            <h1 className="text-xl font-bold text-neutral-darkest">Welcome, {user.name}!</h1>
                            <p className="text-xs text-neutral-medium">Your personal dashboard.</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-4">
                        <Link to="/marketplace" className="hidden sm:inline-flex items-center gap-x-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 border border-slate-200 transition">
                            Marketplace
                        </Link>

                        {/* Cart Link with Badge */}
                        <Link to="/cart" className="relative p-2 text-neutral-dark hover:text-primary transition">
                            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            {cartCount > 0 && (
                                <span className="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full">
                                    {cartCount}
                                </span>
                            )}
                        </Link>

                        <Link to="/post-task" className="hidden sm:inline-flex bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition">
                            + Post New Task
                        </Link>
                        <Link to="/" className="text-sm font-semibold text-neutral-dark hover:text-red-600 transition">Log Out</Link>
                    </div>
                </div>
            </header>

            <div className="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

                {/* --- Marketplace Banner --- */}
                {showBanner && (
                    <section className="bg-primary rounded-xl shadow-lg mb-8 relative overflow-hidden text-white p-6 flex flex-col sm:flex-row items-center justify-between animate-fade-in-up">
                        <div className="z-10">
                            <h2 className="text-xl font-bold">Explore the Marketplace</h2>
                            <p className="text-purple-200 text-sm mt-1">Find assistive devices and tools.</p>
                        </div>
                        <Link to="/marketplace" className="mt-4 sm:mt-0 bg-white text-primary font-bold px-6 py-2 rounded-lg shadow hover:bg-neutral-100 transition z-10">
                            Browse Products &rarr;
                        </Link>
                        <button onClick={() => setShowBanner(false)} className="absolute top-2 right-2 text-white/50 hover:text-white">
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </section>
                )}

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* --- Main Column --- */}
                    <main className="lg:col-span-2 space-y-8">

                        {/* 1. Open Tasks */}
                        <section className="animate-fade-in-up">
                            <div className="flex justify-between items-center mb-4">
                                <h2 className="text-lg font-bold text-neutral-darkest">Your Posted Tasks</h2>
                                <label className="flex items-center gap-2 text-sm text-neutral-medium cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={showCompleted}
                                        onChange={(e) => setShowCompleted(e.target.checked)}
                                        className="rounded"
                                    />
                                    Show Completed
                                </label>
                            </div>
                            <div className="space-y-4">
                                {tasks.length === 0 && <p className="text-gray-500">No tasks posted yet.</p>}
                                {tasks
                                    .filter(task => showCompleted || !['completed', 'cancelled'].includes(task.status))
                                    .map(task => (
                                        <div key={task.id} className="bg-white p-5 rounded-xl border border-blue-200 shadow-sm">
                                            <div className="flex justify-between items-start">
                                                <div className="flex-1">
                                                    <h3 className="font-bold text-blue-800">{task.title}</h3>
                                                    <p className="text-sm text-neutral-medium">Status: {task.status}</p>
                                                    {task.status === 'in_progress' && task.caregiver && (
                                                        <p className="text-xs text-blue-600 mt-1">Helper: {task.caregiver.name}</p>
                                                    )}
                                                    {task.status === 'accepted' && task.caregiver && (
                                                        <p className="text-xs text-green-600 mt-1">Assigned to: {task.caregiver.name}</p>
                                                    )}
                                                    <p className="text-xs text-neutral-400">{task.description}</p>
                                                </div>
                                                <div className="flex flex-col items-end gap-2">
                                                    <span className={`text-xs font-bold px-2 py-1 rounded-full ${task.status === 'open' ? 'bg-green-100 text-green-600' :
                                                        task.status === 'in_progress' ? 'bg-blue-100 text-blue-600' :
                                                            task.status === 'completed' ? 'bg-purple-100 text-purple-600' :
                                                                'bg-gray-100 text-gray-600'
                                                        }`}>
                                                        {task.status.toUpperCase()}
                                                    </span>
                                                    {console.log('Task:', task.id, 'Status:', task.status, 'started_at:', task.started_at, 'Type:', typeof task.started_at)}
                                                    {task.status === 'in_progress' && (
                                                        <>
                                                            {task.started_at ? (
                                                                <>
                                                                    {console.log('Rendering timer for task:', task.id, 'started_at:', task.started_at)}
                                                                    <div className="bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
                                                                        <div className="text-xs text-blue-600 mb-1">Time Elapsed:</div>
                                                                        <Timer startTime={new Date(task.started_at).getTime()} />
                                                                    </div>
                                                                </>
                                                            ) : (
                                                                <div className="bg-red-50 px-3 py-2 rounded-lg border border-red-200">
                                                                    <div className="text-xs text-red-600">No start time recorded</div>
                                                                </div>
                                                            )}
                                                        </>
                                                    )}
                                                    <div className="flex gap-2">
                                                        {['completed', 'cancelled'].includes(task.status) && (
                                                            <button
                                                                onClick={() => handleRepostTask(task.id)}
                                                                className="text-xs bg-green-50 text-green-600 px-3 py-1 rounded-full hover:bg-green-100 font-semibold"
                                                            >
                                                                🔄 Repost
                                                            </button>
                                                        )}
                                                        {['open', 'in_progress'].includes(task.status) && (
                                                            <button
                                                                onClick={() => handleDeleteTask(task.id)}
                                                                className="text-xs bg-red-50 text-red-600 px-3 py-1 rounded-full hover:bg-red-100 font-semibold"
                                                            >
                                                                🗑️ Delete
                                                            </button>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                            </div>
                        </section>

                        {/* 2. Review Applicants */}
                        <section className="animate-fade-in-up" style={{ animationDelay: '100ms' }}>
                            <h2 className="text-lg font-bold text-neutral-darkest mb-4">Review Applicants</h2>
                            <div className="space-y-4">
                                {tasksWithApplicants.length === 0 ? (
                                    <p className="text-gray-500 text-sm">No new applicants to review.</p>
                                ) : (
                                    tasksWithApplicants.map(task => (
                                        <div key={task.id} className="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
                                            <div
                                                onClick={() => toggleApplicants(task.id)}
                                                className="p-5 flex justify-between items-center cursor-pointer hover:bg-neutral-50 transition"
                                            >
                                                <div>
                                                    <h3 className="font-bold text-neutral-darkest">{task.title}</h3>
                                                    <p className="text-sm text-primary">{task.applicant_count} HelpMate(s) applied</p>
                                                </div>
                                                <svg className={`w-5 h-5 text-neutral-400 transform transition-transform ${openApplicantTask === task.id ? 'rotate-180' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" /></svg>
                                            </div>

                                            {openApplicantTask === task.id && (
                                                <div className="border-t border-neutral-100 bg-neutral-50 p-4 space-y-3">
                                                    {task.applicants.map(app => (
                                                        <div key={app.id} className="flex justify-between items-center bg-white p-3 rounded-lg border border-neutral-100">
                                                            <div className="flex items-center gap-3">
                                                                <img src={app.photo} alt={app.name} className="w-10 h-10 rounded-full" />
                                                                <div>
                                                                    <Link to={`/profile/helpmate/${app.id}`} className="font-bold text-sm hover:text-primary hover:underline">
                                                                        {app.name}
                                                                    </Link>
                                                                    <p className="text-xs text-yellow-500">{app.rating === 'New' ? 'New' : `★ ${app.rating}`}</p>
                                                                </div>
                                                            </div>
                                                            <div className="flex gap-2 items-center">
                                                                <span className={`text-xs font-bold uppercase ${app.status === 'accepted' ? 'text-green-600' : app.status === 'rejected' ? 'text-red-600' : 'text-gray-400'}`}>
                                                                    {app.status}
                                                                </span>
                                                                {app.status === 'pending' && (
                                                                    <>
                                                                        <button
                                                                            onClick={(e) => { e.stopPropagation(); handleApplicationAction(task.id, app.application_id, 'rejected'); }}
                                                                            className="bg-red-50 text-red-600 text-xs font-bold px-3 py-1.5 rounded-full hover:bg-red-100 transition"
                                                                        >
                                                                            Delete
                                                                        </button>
                                                                        <button
                                                                            onClick={(e) => { e.stopPropagation(); handleApplicationAction(task.id, app.application_id, 'accepted'); }}
                                                                            className="bg-secondary text-white text-xs font-bold px-3 py-1.5 rounded-full hover:bg-green-600 transition"
                                                                        >
                                                                            Hire
                                                                        </button>
                                                                    </>
                                                                )}
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    )))}
                            </div>
                        </section>
                    </main>

                    {/* --- Sidebar --- */}
                    <aside className="space-y-8">

                        {/* Pending Payments */}
                        <section className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up" style={{ animationDelay: '200ms' }}>
                            <h2 className="text-lg font-bold text-neutral-darkest mb-4">Pending Payments</h2>
                            <ul className="space-y-4">
                                {PENDING_PAYMENTS.map(pay => (
                                    <li key={pay.id} className="border-b border-neutral-100 pb-3 last:border-0 last:pb-0">
                                        <div className="flex justify-between items-center mb-2">
                                            <div>
                                                <p className="font-medium text-sm text-neutral-darkest">{pay.title}</p>
                                                <p className="text-xs text-neutral-medium">w/ {pay.helpmate}</p>
                                            </div>
                                            <span className="font-bold text-neutral-darkest">${pay.amount}</span>
                                        </div>
                                        <Link
                                            to="/service-payment"
                                            className="block w-full text-center bg-green-50 text-green-700 text-xs font-bold py-2 rounded-lg hover:bg-green-100 transition"
                                        >
                                            Confirm & Pay
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </section>

                        {/* Quick Access */}
                        <section className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up" style={{ animationDelay: '300ms' }}>
                            <h2 className="text-lg font-bold text-neutral-darkest mb-4">Quick Access</h2>
                            <div className="grid grid-cols-2 gap-3">
                                {/* Trusted Contacts Link */}
                                <Link to="/trusted-contacts" className="col-span-2 p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center flex items-center justify-center text-neutral-dark">
                                    Manage Trusted Contacts
                                </Link>

                                <Link to={`/profile/user/${user?.id}`} className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center flex items-center justify-center text-neutral-dark">
                                    My Profile
                                </Link>
                                <Link to="/payment" className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center flex items-center justify-center text-neutral-dark">
                                    Payment History
                                </Link>
                                <Link to="/resources" className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center flex items-center justify-center text-neutral-dark">
                                    Resources
                                </Link>
                                <Link to="/marketplace" className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center flex items-center justify-center text-neutral-dark">
                                    Find Help
                                </Link>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>

            <Chatbot />
        </div>
    );
};

export default UserDashboard;
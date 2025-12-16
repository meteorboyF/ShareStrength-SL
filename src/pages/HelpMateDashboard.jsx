import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

// --- MOCK DATA ---
const HELPMATE_PROFILE = {
  name: "Jamie The HelpMate",
  email: "jamie@example.com",
  photo: "https://placehold.co/150x150",
  rating: 4.85,
  skills: ["Mobility Support", "Driving", "Tech Support"],
  total_earnings: 1250.00,
  completed_jobs: 45
};

const MOCK_OFFERS = [
  { id: 1, user_name: "Grandma Clara", title: "Weekend Gardening", hourly_rate: 30 }
];

const MOCK_ACTIVE_JOBS = [
  { 
    id: 101, 
    title: "Grocery Run", 
    user_name: "Mr. Fredricksen", 
    start_time: Date.now() - 7500000 // Started ~2 hours ago
  }
];

const INITIAL_AVAILABLE_TASKS = [
  { 
    id: 501, 
    title: "Morning Walk Companion", 
    description: "Need someone to walk with me in the park for 1 hour.", 
    user_name: "Alice M.", 
    user_photo: "https://placehold.co/50", 
    hourly_rate: 20, 
    skill: "Companion", 
    urgency: "Low" 
  },
  { 
    id: 502, 
    title: "Fix Printer & Wifi", 
    description: "My printer won't connect to the computer.", 
    user_name: "Bob S.", 
    user_photo: "https://placehold.co/50", 
    hourly_rate: 35, 
    skill: "Tech Support", 
    urgency: "Medium" 
  },
  { 
    id: 503, 
    title: "Drive to Doctor", 
    description: "Appointment at City Hospital at 2 PM.", 
    user_name: "Sarah C.", 
    user_photo: "https://placehold.co/50", 
    hourly_rate: 40, 
    skill: "Driving", 
    urgency: "High" 
  }
];

const INITIAL_APPLIED = [
  { id: 901, title: "Library Assistant", user_name: "City Library" }
];

const HelpMateDashboard = () => {
  // State
  const [availableTasks, setAvailableTasks] = useState(INITIAL_AVAILABLE_TASKS);
  const [appliedJobs, setAppliedJobs] = useState(INITIAL_APPLIED);
  const [offers, setOffers] = useState(MOCK_OFFERS);
  const [activeJobs, setActiveJobs] = useState(MOCK_ACTIVE_JOBS);
  
  // Modal State
  const [showModal, setShowModal] = useState(false);
  const [selectedTask, setSelectedTask] = useState(null);

  // --- Live Timer Component ---
  const Timer = ({ startTime }) => {
    const [elapsed, setElapsed] = useState("00:00:00");

    useEffect(() => {
      const interval = setInterval(() => {
        const now = Date.now();
        const diff = now - startTime;
        if (diff < 0) return;

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        setElapsed(
          `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
        );
      }, 1000);
      return () => clearInterval(interval);
    }, [startTime]);

    return <span className="font-mono text-sm text-slate-700">{elapsed}</span>;
  };

  // Handlers
  const handleApplyClick = (task) => {
    setSelectedTask(task);
    setShowModal(true);
  };

  const confirmApply = () => {
    if (!selectedTask) return;

    // Remove from available
    setAvailableTasks(availableTasks.filter(t => t.id !== selectedTask.id));
    
    // Add to applied
    setAppliedJobs([{ id: Date.now(), title: selectedTask.title, user_name: selectedTask.user_name }, ...appliedJobs]);
    
    setShowModal(false);
    setSelectedTask(null);
  };

  const handleOfferAction = (id, action) => {
    // Remove offer from list for demo
    setOffers(offers.filter(o => o.id !== id));
    alert(`Offer ${action}ed!`);
  };

  return (
    <div className="min-h-screen bg-slate-50 font-sans text-slate-900">
      
      {/* Header */}
      <header className="bg-white shadow-sm sticky top-0 z-40 border-b border-green-100">
         <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
             <div>
                 <h1 className="text-2xl font-bold tracking-tight text-slate-900">Welcome, {HELPMATE_PROFILE.name}!</h1>
                 <p className="text-xs text-slate-500">Manage your jobs and find new tasks.</p>
             </div>
             <div className="flex items-center gap-3">
                 <button className="hidden sm:inline-flex items-center gap-x-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 border border-slate-200 transition">
                     Edit Profile
                 </button>
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
                        <img className="h-16 w-16 rounded-full object-cover border-2 border-green-400" src={HELPMATE_PROFILE.photo} alt="Profile" />
                        <div>
                            <h3 className="font-bold text-lg">{HELPMATE_PROFILE.name}</h3>
                            <p className="text-sm text-green-100">{HELPMATE_PROFILE.email}</p>
                        </div>
                    </div>
                    <div className="mt-4 flex items-center justify-between text-sm">
                        <span className="font-semibold text-green-200">Rating:</span>
                        <span className="font-bold text-yellow-300 flex items-center gap-1">
                            ★ {HELPMATE_PROFILE.rating}
                        </span>
                    </div>
                    <div className="mt-4 pt-4 border-t border-green-600">
                        <h4 className="font-semibold text-sm text-green-200 mb-2">My Skills</h4>
                        <div className="flex flex-wrap gap-2">
                           {HELPMATE_PROFILE.skills.map(skill => (
                            <span key={skill} className="bg-green-600 text-white text-xs font-medium px-2 py-1 rounded-full border border-green-500">{skill}</span>
                           ))}
                        </div>
                    </div>
                </section>

                {/* Earnings */}
                <section className="bg-green-800 text-white p-6 rounded-xl shadow-lg animate-fade-in-up" style={{animationDelay: '100ms'}}>
                     <h3 className="font-semibold mb-2">My Stats</h3>
                     <div className="space-y-3">
                        <div className="flex justify-between items-baseline">
                            <span className="text-sm text-green-200">Total Earnings</span>
                            <span className="text-2xl font-bold text-white">${HELPMATE_PROFILE.total_earnings.toFixed(2)}</span>
                        </div>
                        <div className="flex justify-between items-baseline">
                            <span className="text-sm text-green-200">Completed Jobs</span>
                            <span className="text-2xl font-bold text-white">{HELPMATE_PROFILE.completed_jobs}</span>
                        </div>
                     </div>
                </section>

                {/* Applied Jobs List */}
                <section className="bg-white p-4 rounded-lg border border-slate-200 shadow-sm animate-fade-in-up" style={{animationDelay: '200ms'}}>
                    <h4 className="font-semibold text-sm mb-3 text-slate-600">Applied Jobs</h4>
                    <ul className="space-y-2">
                        {appliedJobs.length === 0 ? (
                            <li className="text-xs text-slate-400">No active applications.</li>
                        ) : (
                            appliedJobs.map(job => (
                                <li key={job.id} className="text-sm p-2 bg-slate-50 rounded-md border border-slate-100 flex justify-between items-center">
                                    <span className="font-medium text-slate-700">{job.title}</span>
                                    <span className="text-xs text-slate-500">{job.user_name}</span>
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
                <section className="animate-fade-in-up" style={{animationDelay: '100ms'}}>
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
                                                <p className="text-xs text-blue-600">for {job.user_name}</p>
                                            </div>
                                            <div className="flex items-center gap-3">
                                                <div className="bg-white px-3 py-1 rounded border border-blue-100 shadow-sm">
                                                    <Timer startTime={job.start_time} />
                                                </div>
                                                <button className="text-xs font-bold bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition">End Work</button>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </section>

                {/* 3. Available Tasks Feed */}
                <section className="animate-fade-in-up" style={{animationDelay: '200ms'}}>
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
                                                    <Link to={`/profile/user/${task.id === 501 ? 501 : 101}`} className="hover:text-green-600 hover:underline">
                                                        {task.user_name}
                                                    </Link>
                                                </p>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-xl font-bold text-slate-900">${task.hourly_rate}</p>
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
            <div className="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
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
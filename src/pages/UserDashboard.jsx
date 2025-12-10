import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import Chatbot from '../components/Chatbot';

// --- MOCK DATA (Simulating your Database) ---
const USER_DATA = {
  name: "Alex Johnson",
  profile_photo: "https://placehold.co/100x100", // Placeholder image
  cart_count: 2
};

const IN_PROGRESS_TASKS = [
  { id: 101, title: "Math Tutoring", helpmate: "Sarah Smith", start_time: Date.now() - 3600000 } // Started 1 hour ago
];

const TASKS_WITH_APPLICANTS = [
  { 
    id: 201, 
    title: "Grocery Shopping Assistance", 
    applicant_count: 2,
    applicants: [
        { id: 5, name: "John Doe", rating: 4.8, photo: "https://placehold.co/50" },
        { id: 6, name: "Jane Roe", rating: 4.5, photo: "https://placehold.co/50" }
    ]
  }
];

const PENDING_PAYMENTS = [
  { id: 301, title: "Garden Cleaning", helpmate: "Mike Ross", amount: 45.50 }
];

const UserDashboard = () => {
  const [openApplicantTask, setOpenApplicantTask] = useState(null);
  const [showBanner, setShowBanner] = useState(true);

  // Toggle Accordion for Applicants
  const toggleApplicants = (taskId) => {
    setOpenApplicantTask(openApplicantTask === taskId ? null : taskId);
  };

  return (
    <div className="min-h-screen bg-neutral-light font-sans text-neutral-dark">
      
      {/* --- Header --- */}
      <header className="bg-white shadow-sm sticky top-0 z-40">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div className="flex items-center gap-4">
                <img src={USER_DATA.profile_photo} alt="Profile" className="h-12 w-12 rounded-full border-2 border-primary object-cover" />
                <div>
                    <h1 className="text-xl font-bold text-neutral-darkest">Welcome, {USER_DATA.name}!</h1>
                    <p className="text-xs text-neutral-medium">Your personal dashboard.</p>
                </div>
            </div>
            <div className="flex items-center gap-4">
                <button className="relative p-2 text-neutral-dark hover:text-primary transition">
                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    {USER_DATA.cart_count > 0 && <span className="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full">{USER_DATA.cart_count}</span>}
                </button>
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
                <button className="mt-4 sm:mt-0 bg-white text-primary font-bold px-6 py-2 rounded-lg shadow hover:bg-neutral-100 transition z-10">
                    Browse Products &rarr;
                </button>
                <button onClick={() => setShowBanner(false)} className="absolute top-2 right-2 text-white/50 hover:text-white">
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </section>
        )}

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* --- Main Column --- */}
            <main className="lg:col-span-2 space-y-8">
                
                {/* 1. In Progress Tasks */}
                <section className="animate-fade-in-up">
                    <h2 className="text-lg font-bold text-neutral-darkest mb-4">Tasks In Progress</h2>
                    <div className="space-y-4">
                        {IN_PROGRESS_TASKS.map(task => (
                            <div key={task.id} className="bg-white p-5 rounded-xl border border-blue-200 shadow-sm flex justify-between items-center">
                                <div>
                                    <h3 className="font-bold text-blue-800">{task.title}</h3>
                                    <p className="text-sm text-neutral-medium">with {task.helpmate}</p>
                                </div>
                                <div className="text-right">
                                    <span className="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded-full">ACTIVE</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>

                {/* 2. Review Applicants */}
                <section className="animate-fade-in-up" style={{ animationDelay: '100ms' }}>
                    <h2 className="text-lg font-bold text-neutral-darkest mb-4">Review Applicants</h2>
                    <div className="space-y-4">
                        {TASKS_WITH_APPLICANTS.map(task => (
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
                                
                                {/* Accordion Body */}
                                {openApplicantTask === task.id && (
                                    <div className="border-t border-neutral-100 bg-neutral-50 p-4 space-y-3">
                                        {task.applicants.map(app => (
                                            <div key={app.id} className="flex justify-between items-center bg-white p-3 rounded-lg border border-neutral-100">
                                                <div className="flex items-center gap-3">
                                                    <img src={app.photo} alt={app.name} className="w-10 h-10 rounded-full" />
                                                    <div>
                                                        <p className="font-bold text-sm">{app.name}</p>
                                                        <p className="text-xs text-yellow-500">★ {app.rating}</p>
                                                    </div>
                                                </div>
                                                <button className="bg-secondary text-white text-xs font-bold px-3 py-1.5 rounded-full hover:bg-green-600 transition">Hire</button>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        ))}
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
                                <button className="w-full bg-green-50 text-green-700 text-xs font-bold py-2 rounded-lg hover:bg-green-100 transition">Confirm & Pay</button>
                            </li>
                        ))}
                    </ul>
                </section>

{/* Quick Access */}
<section className="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-fade-in-up" style={{ animationDelay: '300ms' }}>
    <h2 className="text-lg font-bold text-neutral-darkest mb-4">Quick Access</h2>
    <div className="grid grid-cols-2 gap-3">
        {/* Placeholder Button */}
        <button className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center text-neutral-dark">
            My Profile
        </button>

        {/* LINK TO PAYMENT HISTORY */}
        <Link to="/payment" className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center flex items-center justify-center text-neutral-dark">
            Payment History
        </Link>

        {/* Placeholder Button */}
<Link to="/resources" className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center flex items-center justify-center text-neutral-dark">
    Resources
</Link>

        {/* Placeholder Button */}
        <button className="p-3 border border-neutral-200 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-primary/50 transition text-center text-neutral-dark">
            Find Help
        </button>
    </div>
</section>
            </aside>
        </div>
      </div>

      {/* Chatbot Widget Overlay */}
      <Chatbot />

    </div>
  );
};

export default UserDashboard;
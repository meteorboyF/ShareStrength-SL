import React, { useState } from 'react';
import { Link } from 'react-router-dom';

// --- MOCK DATA ---
const MY_APPLICATIONS = [
  { 
    id: 101, 
    title: "Grocery Run", 
    user_name: "Mr. Fredricksen", 
    description: "Need help buying weekly groceries from Whole Foods. Heavy lifting required.",
    skill: "Shopping",
    rate: "$25 /hr",
    status: "accepted", // accepted, pending, rejected
    date: "2025-10-15"
  },
  { 
    id: 102, 
    title: "Library Assistant", 
    user_name: "City Library", 
    description: "Assisting with organizing the community book drive.",
    skill: "Other",
    rate: "$18 /hr",
    status: "pending",
    date: "2025-10-18"
  },
  { 
    id: 103, 
    title: "Tech Support for iPad", 
    user_name: "Sarah C.", 
    description: "My iPad won't connect to the printer.",
    skill: "Tech Support",
    rate: "$35 /hr",
    status: "rejected",
    date: "2025-10-10"
  }
];

// Mock contacts to show when revealed
const MOCK_CONTACTS = [
    { name: "Martha (Daughter)", phone: "+1 (555) 012-3456" },
    { name: "Dr. Stevens", phone: "+1 (555) 098-7654" }
];

const TaskStatus = () => {
  // Track which task's emergency contacts are being viewed
  const [viewingContactsId, setViewingContactsId] = useState(null);

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
            {MY_APPLICATIONS.length === 0 ? (
                <div className="text-center py-20 bg-white rounded-xl shadow-sm">
                    <p className="text-neutral-medium">You haven't applied to any tasks yet.</p>
                    <Link to="/helpmate-dashboard" className="mt-4 inline-block text-green-600 font-bold">Browse Jobs</Link>
                </div>
            ) : (
                MY_APPLICATIONS.map((task) => (
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

                            {/* CONDITIONAL: CONTACTS DISPLAY */}
                            {viewingContactsId === task.id && (
                                <div className="mt-4 bg-red-50 border border-red-200 rounded-lg p-4 animate-fade-in-up">
                                    <h4 className="font-bold text-red-800 text-sm mb-2 flex items-center gap-2">
                                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" /></svg>
                                        Emergency Trusted Contacts for {task.user_name}
                                    </h4>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        {MOCK_CONTACTS.map((contact, idx) => (
                                            <div key={idx} className="bg-white p-3 rounded-lg border border-red-100 shadow-sm flex justify-between items-center">
                                                <span className="font-semibold text-neutral-700">{contact.name}</span>
                                                <a href={`tel:${contact.phone}`} className="text-blue-600 font-bold text-sm hover:underline">{contact.phone}</a>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Action Column */}
                        <div className="flex md:flex-col justify-end gap-2 md:w-44">
                            {task.status === 'accepted' && (
                                <>
                                    <button className="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700 transition text-sm shadow-sm">
                                        Start Job
                                    </button>
                                    
                                    {/* TOGGLE CONTACTS BUTTON */}
                                    <button 
                                        onClick={() => setViewingContactsId(viewingContactsId === task.id ? null : task.id)}
                                        className="w-full bg-red-50 text-red-600 border border-red-200 font-bold py-2 px-4 rounded-lg hover:bg-red-100 transition text-sm flex items-center justify-center gap-1"
                                    >
                                        {viewingContactsId === task.id ? 'Hide Info' : 'Emergency Info'}
                                    </button>
                                </>
                            )}
                            <button className="w-full border border-neutral-300 text-neutral-dark font-semibold py-2 px-4 rounded-lg hover:bg-neutral-50 transition text-sm">
                                View Details
                            </button>
                        </div>

                    </div>
                ))
            )}
        </div>

      </div>
    </div>
  );
};

export default TaskStatus;
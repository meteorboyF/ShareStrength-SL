import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import api from '../services/api';

// Define skills for the form
const SKILLS = [
    { name: "Physical Assistance", icon: <path d="M12 22.2528C17.5228 22.2528 22 17.7757 22 12.2528C22 6.72993 17.5228 2.25278 12 2.25278C6.47715 2.25278 2 6.72993 2 12.2528C2 17.7757 6.47715 22.2528 12 22.2528Z M12 12.2528H16 M12 12.2528V16.2528" /> },
    { name: "Transport & Errands", icon: <path d="M19 12.2528H21M17 17.2528L18 16.2528M7 7.25278L6 8.25278M5 12.2528H3M12 5.25278V3.25278M12 21.2528V19.2528M12 12.2528L7 17.2528L5 15.2528M9.5 10.7528L12 12.2528Z" /> },
    { name: "Household Help", icon: <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /> },
    { name: "Companionship", icon: <path d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /> },
    { name: "Tech & Admin", icon: <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /> },
    { name: "Other Support", icon: <path d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" /> }
];

const PostTask = () => {
    const navigate = useNavigate();
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        skill_required: '',
        urgency: 'Medium',
        hourly_rate: 25
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        if (name === 'hourly_rate') {
            let val = parseInt(value);
            if (val < 10) val = 10;
            setFormData({ ...formData, hourly_rate: val });
        } else {
            setFormData({ ...formData, [name]: value });
        }
    };

    const handleSkillSelect = (skillName) => {
        setFormData({ ...formData, skill_required: skillName });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const payload = {
                title: formData.title,
                description: formData.description,
                location: 'Remote', // Default or add field
                hourly_rate: formData.hourly_rate,
                urgency: formData.urgency,
                skill_required: formData.skill_required,
                scheduled_at: new Date().toISOString() // Default to now
            };

            await api.post('/tasks', payload);
            alert("Task Posted Successfully!");
            navigate('/dashboard');
        } catch (err) {
            console.error(err);
            alert('Failed to post task: ' + (err.response?.data?.message || err.message));
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center p-4 bg-neutral-light font-sans">
            <div className="w-full max-w-5xl mx-auto animate-fade-in-up">
                <div className="bg-white rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">

                    {/* Left Side: Information & Tips (Indigo Background) */}
                    <div className="p-8 bg-primary text-white order-last md:order-first flex flex-col justify-between">
                        <div>
                            <Link to="/dashboard" className="text-sm font-semibold text-indigo-200 hover:text-white flex items-center gap-1 mb-8">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fillRule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clipRule="evenodd" /></svg>
                                Back to Portal
                            </Link>
                            <h1 className="text-3xl font-bold tracking-tight">Post a New Task</h1>
                            <p className="mt-4 text-indigo-200">Describe the support you need, and let our community of verified HelpMates find you.</p>

                            <div className="mt-8 pt-6 border-t border-indigo-500 border-opacity-50 space-y-6">
                                <div className="flex gap-4">
                                    <div className="flex-shrink-0"><svg className="h-6 w-6 text-indigo-300" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg></div>
                                    <div>
                                        <h4 class="font-semibold">Be Specific</h4>
                                        <p class="text-sm text-indigo-200">Clearly describe the task, including any specific requirements or times.</p>
                                    </div>
                                </div>
                                <div className="flex gap-4">
                                    <div className="flex-shrink-0"><svg className="h-6 w-6 text-indigo-300" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                    <div>
                                        <h4 class="font-semibold">Offer a Fair Rate</h4>
                                        <p class="text-sm text-indigo-200">A competitive hourly rate will attract more qualified and experienced HelpMates.</p>
                                    </div>
                                </div>
                                <div className="flex gap-4">
                                    <div className="flex-shrink-0"><svg className="h-6 w-6 text-indigo-300" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286zm0 13.036h.008v.008h-.008v-.008z" /></svg></div>
                                    <div>
                                        <h4 class="font-semibold">Safety First</h4>
                                        <p class="text-sm text-indigo-200">Remember, all HelpMates are verified by our platform for your peace of mind.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Side: Form */}
                    <div className="p-8">
                        <form onSubmit={handleSubmit} className="space-y-8">
                            {/* Title */}
                            <div>
                                <label htmlFor="title" className="block text-sm font-semibold text-neutral-dark mb-2">Task Title</label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    value={formData.title}
                                    onChange={handleChange}
                                    className="block w-full rounded-lg border-neutral-200 bg-neutral-light py-3 px-4 text-neutral-dark shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/50"
                                    placeholder="A clear and concise title"
                                    required
                                />
                            </div>

                            {/* Description */}
                            <div>
                                <label htmlFor="description" className="block text-sm font-semibold text-neutral-dark mb-2">Description</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    value={formData.description}
                                    onChange={handleChange}
                                    className="block w-full rounded-lg border-neutral-200 bg-neutral-light py-3 px-4 text-neutral-dark shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/50"
                                    placeholder="Describe the task in detail..."
                                    required
                                ></textarea>
                            </div>

                            {/* Skill Category Grid */}
                            <div>
                                <label className="block text-sm font-semibold text-neutral-dark mb-2">Skill Category</label>
                                <div className="grid grid-cols-3 gap-3">
                                    {SKILLS.map((skill) => (
                                        <div key={skill.name} onClick={() => handleSkillSelect(skill.name)} className="cursor-pointer">
                                            <div className={`p-3 border-2 rounded-lg flex flex-col items-center justify-center gap-2 text-center transition-all duration-200 ${formData.skill_required === skill.name
                                                ? 'border-primary bg-primary/10 text-primary-dark ring-2 ring-primary/50'
                                                : 'border-neutral-200 hover:border-primary/50 hover:bg-neutral-50 text-neutral-medium'
                                                }`}>
                                                <svg className={`h-8 w-8 ${formData.skill_required === skill.name ? 'text-primary' : 'text-neutral-400'}`} fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                                    {skill.icon}
                                                </svg>
                                                <span className={`text-xs font-semibold ${formData.skill_required === skill.name ? 'text-primary-dark' : 'text-neutral-dark'}`}>
                                                    {skill.name}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                {/* Hidden input for HTML5 validation if needed, or handled via state */}
                                <input type="hidden" name="skill_required" value={formData.skill_required} required />
                            </div>

                            {/* Urgency and Rate Row */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
                                <div>
                                    <label className="block text-sm font-semibold text-neutral-dark mb-2">Urgency</label>
                                    <div className="flex rounded-md shadow-sm">
                                        {['Low', 'Medium', 'High'].map((level, idx) => (
                                            <button
                                                type="button"
                                                key={level}
                                                onClick={() => setFormData({ ...formData, urgency: level })}
                                                className={`flex-1 px-4 py-2 text-sm font-medium border transition-colors duration-200 
                                            ${idx === 0 ? 'rounded-l-md' : ''} 
                                            ${idx === 2 ? 'rounded-r-md' : ''}
                                            ${formData.urgency === level
                                                        ? 'bg-primary text-white border-primary z-10'
                                                        : 'bg-white text-neutral-medium border-neutral-300 hover:bg-neutral-50'
                                                    }
                                        `}
                                            >
                                                {level}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                                <div>
                                    <label htmlFor="hourly_rate" className="block text-sm font-semibold text-neutral-dark mb-2">Proposed Rate ($/hr)</label>
                                    <div className="relative">
                                        <input
                                            type="number"
                                            id="hourly_rate"
                                            name="hourly_rate"
                                            value={formData.hourly_rate}
                                            onChange={handleChange}
                                            className="block w-full rounded-lg border-neutral-200 bg-neutral-light py-3 px-4 text-neutral-dark shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/50 text-center font-bold"
                                            step="1" min="10" max="100" required
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="pt-2">
                                <button type="submit" className="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-base font-semibold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all transform hover:scale-105">
                                    Post Your Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PostTask;
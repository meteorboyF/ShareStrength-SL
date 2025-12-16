import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';

const SKILLS = [
  { name: "Tutoring", icon: <path d="M12 6.25278V19.2528M12 6.25278C10.8321 5.46388 9.23129 5.44496 8 6.25278C6.76871 7.06061 6.76871 8.68594 8 9.49377C9.23129 10.3016 10.8321 10.3205 12 9.49377M12 6.25278C13.1679 5.46388 14.7687 5.44496 16 6.25278C17.2313 7.06061 17.2313 8.68594 16 9.49377C14.7687 10.3016 13.1679 10.3205 12 9.49377M3 21.2528H21"/> },
  { name: "Reading Assistance", icon: <path d="M4 19.2528V5.25278C4 4.14821 4.89543 3.25278 6 3.25278H14.5L19 7.75278V19.2528C19 20.3574 18.1046 21.2528 17 21.2528H6C4.89543 21.2528 4 20.3574 4 19.2528Z"/> },
  { name: "Mobility Support", icon: <path d="M12 22.2528C17.5228 22.2528 22 17.7757 22 12.2528C22 6.72993 17.5228 2.25278 12 2.25278C6.47715 2.25278 2 6.72993 2 12.2528C2 17.7757 6.47715 22.2528 12 22.2528Z M12 12.2528H16 M12 12.2528V16.2528"/> },
  { name: "Driving", icon: <path d="M19 12.2528H21M17 17.2528L18 16.2528M7 7.25278L6 8.25278M5 12.2528H3M12 5.25278V3.25278M12 21.2528V19.2528M12 12.2528L7 17.2528L5 15.2528L9.5 10.7528L12 12.2528Z"/> },
  { name: "Shopping", icon: <path d="M3 3.25278H5.20371C5.66698 3.25278 6.06648 3.52989 6.22301 3.96781L10.223 15.9678C10.3795 16.4057 10.779 16.6828 11.2423 16.6828H17.5M7 21.2528C7.55228 21.2528 8 20.8051 8 20.2528C8 19.7005 7.55228 19.2528 7 19.2528C6.44772 19.2528 6 19.7005 6 20.2528C6 20.8051 6.44772 21.2528 7 21.2528ZM17 21.2528C17.5523 21.2528 18 20.8051 18 20.2528C18 19.7005 17.5523 19.2528 17 19.2528C16.4477 19.2528 16 19.7005 16 20.2528C16 20.8051 16.4477 21.2528 17 21.2528Z"/> },
  { name: "Housekeeping", icon: <path d="M12.9875 3.32832L21.3621 10.9571C21.7225 11.2863 21.8028 11.8091 21.5701 12.2458L16.8824 21.0519C16.6496 21.4886 16.1017 21.6705 15.6264 21.5201L4.85175 17.674C4.37648 17.5235 4.02987 17.0673 4.02987 16.568V4.88721C4.02987 4.38792 4.37648 3.93175 4.85175 3.78129L12.0125 1.41921C12.4878 1.26875 13.0357 1.45063 13.2684 1.88731L13.8447 2.96425C13.911 3.08558 13.8584 3.23232 13.7371 3.29865L3.84471 8.24531"/> },
  { name: "Tech Support", icon: <path d="M7 17.2528H17M7 21.2528H17M4 4.25278H20V13.2528C20 14.3574 19.1046 15.2528 18 15.2528H6C4.89543 15.2528 4 14.3574 4 13.2528V4.25278Z"/> },
  { name: "Companion", icon: <path d="M8 12.2528C9.65685 12.2528 11 10.9097 11 9.25278C11 7.59593 9.65685 6.25278 8 6.25278C6.34315 6.25278 5 7.59593 5 9.25278C5 10.9097 6.34315 12.2528 8 12.2528ZM16 12.2528C17.6569 12.2528 19 10.9097 19 9.25278C19 7.59593 17.6569 6.25278 16 6.25278C14.3431 6.25278 13 7.59593 13 9.25278C13 10.9097 14.3431 12.2528 16 12.2528ZM11.4011 15.5539C10.7028 15.8341 9.94827 16.0028 9.16641 16.0028C6.67876 16.0028 4.54922 14.7331 3.32422 12.8398M18.6758 12.8398C18.0622 13.8833 17.0911 14.7331 15.9082 15.2592"/> },
  { name: "Other", icon: <path d="M6 12.2528H18M12 6.25278V18.2528"/> }
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
    // Handle the slider logic separately to ensure types match
    if (name === 'hourly_rate') {
        let val = parseInt(value);
        if (val > 100) val = 100;
        if (val < 10) val = 10;
        setFormData({ ...formData, hourly_rate: val });
    } else {
        setFormData({ ...formData, [name]: value });
    }
  };

  const handleSkillSelect = (skillName) => {
    setFormData({ ...formData, skill_required: skillName });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log("Posting Task:", formData);
    
    // --- DEMO LOGIC ---
    // In a real app, this would be an API call to the backend.
    alert("Task Posted Successfully! (Demo)");
    navigate('/dashboard');
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
                                    <div className={`p-3 border-2 rounded-lg flex flex-col items-center justify-center gap-2 text-center transition-all duration-200 ${
                                        formData.skill_required === skill.name 
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
                                        onClick={() => setFormData({...formData, urgency: level})}
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
                                 <input 
                                    type="range" 
                                    min="10" 
                                    max="100" 
                                    name="hourly_rate" // Reusing name for generic handler
                                    value={formData.hourly_rate}
                                    onChange={handleChange}
                                    className="w-full h-2 bg-neutral-200 rounded-lg appearance-none cursor-pointer mt-3 accent-primary"
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
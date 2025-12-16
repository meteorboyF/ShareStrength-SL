import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';

const SKILLS_LIST = [
  "Mobility Support", "Driving", "Cooking", "Housekeeping", 
  "Tech Support", "Companionship", "Reading Assistance"
];

const RegisterHelpMate = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    confirmPassword: '',
    skills: []
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSkillChange = (skill) => {
    if (formData.skills.includes(skill)) {
      setFormData({ ...formData, skills: formData.skills.filter(s => s !== skill) });
    } else {
      setFormData({ ...formData, skills: [...formData.skills, skill] });
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (formData.password !== formData.confirmPassword) {
      alert("Passwords do not match!");
      return;
    }
    console.log("Registering HelpMate:", formData);
    // In a real app, send to backend here
    // Redirect to the newly renamed HelpMate Dashboard
    navigate('/helpmate-dashboard'); 
  };

  return (
    <div className="min-h-screen flex items-center justify-center p-4 bg-neutral-light font-sans">
      <div className="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">
        
        {/* Left Side: Visuals (Secondary/Green Theme) */}
        <div className="w-full md:w-1/2 bg-secondary text-white p-12 hidden md:flex flex-col items-center justify-center text-center relative">
          <div className="relative z-10">
            <h2 className="text-3xl font-bold mb-4">Become a HelpMate</h2>
            <p className="text-green-50 text-lg">
              Turn your compassion and skills into income. Join our team of trusted HelpMates today.
            </p>
          </div>
        </div>

        {/* Right Side: Form */}
        <div className="w-full md:w-1/2 p-8 md:p-12">
          <h2 className="text-2xl font-bold text-center text-neutral-darkest mb-8">HelpMate Registration</h2>
          
          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-sm font-semibold text-neutral-dark mb-1">Full Name</label>
              <input 
                type="text" name="name" required 
                className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-secondary"
                onChange={handleChange}
              />
            </div>
            <div>
              <label className="block text-sm font-semibold text-neutral-dark mb-1">Email Address</label>
              <input 
                type="email" name="email" required 
                className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-secondary"
                onChange={handleChange}
              />
            </div>
            
            {/* Skills Selection */}
            <div>
                <label className="block text-sm font-semibold text-neutral-dark mb-2">Your Skills</label>
                <div className="flex flex-wrap gap-2">
                    {SKILLS_LIST.map(skill => (
                        <button
                            key={skill}
                            type="button"
                            onClick={() => handleSkillChange(skill)}
                            className={`px-3 py-1 text-xs font-bold rounded-full border transition ${
                                formData.skills.includes(skill)
                                ? 'bg-secondary text-white border-secondary'
                                : 'bg-white text-neutral-medium border-neutral-300 hover:border-secondary'
                            }`}
                        >
                            {skill}
                        </button>
                    ))}
                </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-semibold text-neutral-dark mb-1">Password</label>
                <input 
                  type="password" name="password" required 
                  className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-secondary"
                  onChange={handleChange}
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-neutral-dark mb-1">Confirm</label>
                <input 
                  type="password" name="confirmPassword" required 
                  className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-secondary"
                  onChange={handleChange}
                />
              </div>
            </div>

            <button type="submit" className="w-full bg-secondary text-white font-bold py-3 rounded-lg hover:bg-green-600 transition shadow-md">
              Register as HelpMate
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-neutral-medium">
            Already have an account? <Link to="/login" className="text-secondary font-bold hover:underline">Sign In</Link>
          </p>
        </div>
      </div>
    </div>
  );
};

export default RegisterHelpMate;
import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';

const RegisterUser = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    confirmPassword: ''
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (formData.password !== formData.confirmPassword) {
      alert("Passwords do not match!");
      return;
    }
    console.log("Registering User:", formData);
    // --- DEMO LOGIC ---
    // In a real app, send to backend here
    navigate('/dashboard'); // Redirect to User Dashboard
  };

  return (
    <div className="min-h-screen flex items-center justify-center p-4 bg-neutral-light font-sans">
      <div className="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">
        
        {/* Left Side: Visuals */}
        <div className="w-full md:w-1/2 bg-primary text-white p-12 hidden md:flex flex-col items-center justify-center text-center relative">
          <div className="relative z-10">
            <h2 className="text-3xl font-bold mb-4">Find the Perfect HelpMate</h2>
            <p className="text-purple-200 text-lg">
              Join our community to connect with vetted, compassionate HelpMates for your daily needs.
            </p>
          </div>
        </div>

        {/* Right Side: Form */}
        <div className="w-full md:w-1/2 p-8 md:p-12">
          <h2 className="text-2xl font-bold text-center text-neutral-darkest mb-8">Create User Account</h2>
          
          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-sm font-semibold text-neutral-dark mb-1">Full Name</label>
              <input 
                type="text" name="name" required 
                className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-primary"
                onChange={handleChange}
              />
            </div>
            <div>
              <label className="block text-sm font-semibold text-neutral-dark mb-1">Email Address</label>
              <input 
                type="email" name="email" required 
                className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-primary"
                onChange={handleChange}
              />
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-semibold text-neutral-dark mb-1">Password</label>
                <input 
                  type="password" name="password" required 
                  className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-primary"
                  onChange={handleChange}
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-neutral-dark mb-1">Confirm</label>
                <input 
                  type="password" name="confirmPassword" required 
                  className="w-full rounded-lg border-neutral-200 bg-neutral-light p-3 text-neutral-dark focus:ring-2 focus:ring-primary"
                  onChange={handleChange}
                />
              </div>
            </div>

            <button type="submit" className="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-primary-dark transition shadow-md">
              Register
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-neutral-medium">
            Already have an account? <Link to="/login" className="text-primary font-bold hover:underline">Sign In</Link>
          </p>
        </div>
      </div>
    </div>
  );
};

export default RegisterUser;
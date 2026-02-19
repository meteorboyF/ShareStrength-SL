import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { AlertTriangle } from 'lucide-react'; // Icon for the error message

import authService from '../services/authService';

const RegisterUser = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    confirmPassword: ''
  });
  const [error, setError] = useState('');

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
    setError(''); // Clear error when user starts typing again
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (formData.password !== formData.confirmPassword) {
      setError("Passwords do not match!");
      return;
    }

    // Basic client-side password length validation for immediate feedback
    if (formData.password.length < 8) {
        setError("The password field must be at least 8 characters.");
        return;
    }

    try {
      const payload = {
        name: formData.name,
        email: formData.email,
        password: formData.password,
        password_confirmation: formData.confirmPassword,
        account_type: 'pwd' // PWD Role
      };

      await authService.register(payload);
      navigate('/dashboard');
    } catch (err) {
      console.error(err);
      const errorMessage = err.response?.data?.errors?.password?.[0] || 
                         err.response?.data?.message || 
                         err.message;
      setError(errorMessage);
    }
  };

  return (
    // Outer div for the animated background and blobs
    <div className="min-h-screen flex items-center justify-center p-4 relative animated-gradient-bg">
      
      {/* Animated Blob/Shape Elements */}
      <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div className="absolute -top-1/4 left-1/4 w-1/2 h-1/2 bg-blue-500/30 rounded-full filter blur-3xl animated-blob-1"></div>
        <div className="absolute bottom-1/4 -right-1/4 w-1/3 h-1/3 bg-purple-500/30 rounded-full filter blur-3xl animated-blob-2"></div>
      </div>

      {/* Main content card (layered above the background) */}
      <div className="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden relative z-10 animate-fade-in-up">

        {/* Left Side: Visuals with Background Image */}
        <div 
          className="w-full md:w-1/2 hidden md:flex items-center justify-center text-center relative bg-cover bg-center"
          style={{ backgroundImage: "url('https://images.unsplash.com/photo-1543269865-cbf427effbad?q=80&w=2070&auto=format&fit=crop')" }}
        >
          {/* Semi-transparent overlay for text readability */}
          <div className="absolute inset-0 bg-purple-800/70 backdrop-blur-sm"></div>
          <div className="relative z-10 p-12">
            <h2 className="text-3xl font-bold text-white mb-4">Find the Perfect HelpMate</h2>
            <p className="text-purple-200 text-lg">
              Join our community to connect with vetted, compassionate HelpMates for your daily needs.
            </p>
          </div>
        </div>

        {/* Right Side: Form */}
        <div className="w-full md:w-1/2 p-8 md:p-12">
          <h2 className="text-2xl font-bold text-center text-gray-800 mb-8">Create User Account</h2>

          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
              <input
                type="text" name="name" required
                className="w-full rounded-lg border-gray-300 bg-gray-50 p-3 text-gray-800 focus:ring-2 focus:ring-purple-500 outline-none transition"
                onChange={handleChange}
              />
            </div>
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
              <input
                type="email" name="email" required
                className="w-full rounded-lg border-gray-300 bg-gray-50 p-3 text-gray-800 focus:ring-2 focus:ring-purple-500 outline-none transition"
                onChange={handleChange}
              />
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input
                  type="password" name="password" required
                  className="w-full rounded-lg border-gray-300 bg-gray-50 p-3 text-gray-800 focus:ring-2 focus:ring-purple-500 outline-none transition"
                  onChange={handleChange}
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-1">Confirm</label>
                <input
                  type="password" name="confirmPassword" required
                  className="w-full rounded-lg border-gray-300 bg-gray-50 p-3 text-gray-800 focus:ring-2 focus:ring-purple-500 outline-none transition"
                  onChange={handleChange}
                />
              </div>
            </div>

            {/* Inline Error Display Component */}
            {error && (
              <div className="flex items-center bg-red-100 text-red-700 text-sm font-bold px-4 py-3 rounded-lg" role="alert">
                <AlertTriangle className="w-5 h-5 mr-3" />
                <p>{error}</p>
              </div>
            )}

            <button type="submit" className="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition shadow-md">
              Register
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-gray-500">
            Already have an account? <Link to="/login" className="text-purple-600 font-bold hover:underline">Sign In</Link>
          </p>
        </div>
      </div>
    </div>
  );
};
export default RegisterUser;
import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';

const Login = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({ email: '', password: '' });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log("Login Attempt:", formData);
    
    // --- DEMO LOGIN LOGIC ---
    // In a real app, you would check the database here.
    // For now, we force the user to go to the dashboard.
    navigate('/dashboard'); 
  };

  return (
    <div className="min-h-screen flex items-center justify-center p-4 bg-neutral-light font-sans">
      <div className="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">
        
        {/* Left Side: Image and Quote */}
        <div className="w-full md:w-1/2 bg-primary text-white p-12 hidden md:flex flex-col items-center justify-center text-center relative">
          <div 
            className="absolute inset-0 bg-cover bg-center opacity-20" 
            style={{ backgroundImage: "url('/img/indexbg.jpg')" }}
          ></div>
          <div className="relative z-10">
            <Link to="/" className="flex items-center justify-center gap-2 mb-8 hover:opacity-80 transition">
              <img src="/img/logo2.png" alt="ShareStrength Logo" className="h-12 w-auto" />
            </Link>
            <h2 className="text-3xl font-bold mt-4 leading-tight">Your Independence, Supported.</h2>
            <p className="mt-4 text-purple-200 max-w-sm mx-auto">
              Connecting individuals with disabilities to a community of vetted, skilled, and compassionate helpers.
            </p>
          </div>
        </div>

        {/* Right Side: Login Form */}
        <div className="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
          <div className="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 className="text-center text-2xl font-bold leading-9 tracking-tight text-neutral-darkest">
              Sign in to your account
            </h2>
          </div>

          <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
            <form className="space-y-6" onSubmit={handleSubmit}>
              <div>
                <label htmlFor="email" className="block text-sm font-medium leading-6 text-neutral-dark">Email address</label>
                <div className="mt-2">
                  <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    autoComplete="email" 
                    required 
                    value={formData.email}
                    onChange={handleChange}
                    className="block w-full rounded-lg border-0 p-3 text-neutral-dark shadow-sm ring-1 ring-inset ring-neutral-medium/30 placeholder:text-neutral-medium focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 bg-neutral-light/50"
                  />
                </div>
              </div>

              <div>
                <div className="flex items-center justify-between">
                  <label htmlFor="password" class="block text-sm font-medium leading-6 text-neutral-dark">Password</label>
                  <div className="text-sm">
                    <a href="#" className="font-semibold text-primary hover:text-primary-dark">Forgot password?</a>
                  </div>
                </div>
                <div className="mt-2">
                  <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    autoComplete="current-password" 
                    required 
                    value={formData.password}
                    onChange={handleChange}
                    className="block w-full rounded-lg border-0 p-3 text-neutral-dark shadow-sm ring-1 ring-inset ring-neutral-medium/30 placeholder:text-neutral-medium focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 bg-neutral-light/50"
                  />
                </div>
              </div>

              <div>
                <button 
                  type="submit" 
                  className="flex w-full justify-center rounded-lg bg-primary px-3 py-3 text-base font-semibold leading-6 text-white shadow-lg hover:bg-primary-dark focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-transform hover:-translate-y-0.5"
                >
                  Sign in
                </button>
              </div>
            </form>

            <p className="mt-10 text-center text-sm text-neutral-medium">
              Not a member?{' '}
              <a href="/register" className="font-semibold leading-6 text-primary hover:text-primary-dark">Register for a new account</a>
            </p>
            <p className="mt-2 text-center text-sm text-neutral-medium">
              Want to become a HelpMate?{' '}
              <a href="/register-helper" className="font-semibold leading-6 text-primary hover:text-primary-dark">Join as a Helper</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Login;
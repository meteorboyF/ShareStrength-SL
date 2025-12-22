import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom'; // <--- ADD THIS LINE

const Navbar = () => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      // Logic: change style if scrolled past 10px
      setIsScrolled(window.scrollY > 10);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Dynamic classes based on state
  const navClasses = `fixed top-0 left-0 right-0 z-50 py-4 transition-all duration-300 ${
    isScrolled ? 'glassmorphism-header shadow-md' : ''
  }`;

  return (
    <header className={navClasses}>
      <div className="container mx-auto px-6 flex justify-between items-center max-w-7xl">
        <a href="#" className="flex items-center">
           <img src="/img/logo2.png" alt="Logo" className="h-10" />
           {/* Fallback text logic can be handled here if needed */}
        </a>

        {/* Desktop Menu */}
        <div className="hidden md:flex items-center gap-4">

          <Link to="/login" className={`font-semibold hover:text-neutral-200 ${isScrolled ? 'text-neutral-dark' : 'text-white'}`}>Login</Link>
          <Link to="/register-helpmate" className="bg-primary text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-primary-dark transition transform hover:-translate-y-0.5">Become a HelpMate</Link>
        </div>

        {/* Mobile Toggle */}
        <button 
          onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          className={`md:hidden p-2 rounded-md focus:outline-none ${isScrolled ? 'text-neutral-darkest' : 'text-white'}`}
        >
           <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
           </svg>
        </button>
      </div>
      
      {/* Mobile Menu Dropdown */}
      {isMobileMenuOpen && (
        <div className="md:hidden bg-white mt-2 border-t">
            <a href="/login" className="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">Login</a>
            <a href="/register" className="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">Become a HelpMate</a>
        </div>
      )}
    </header>
  );
};

export default Navbar;
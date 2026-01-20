import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const Navbar = () => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const { cartCount } = useCart();

  useEffect(() => {
    const handleScroll = () => {
      // Logic: change style if scrolled past 10px
      setIsScrolled(window.scrollY > 10);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Dynamic classes based on state
  const navClasses = `fixed top-0 left-0 right-0 z-50 py-4 transition-all duration-300 ${isScrolled ? 'glassmorphism-header shadow-md' : ''
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
          <Link to="/marketplace" className={`font-semibold hover:text-neutral-200 ${isScrolled ? 'text-neutral-dark' : 'text-white'}`}>Marketplace</Link>

          <Link to="/cart" className={`relative font-semibold hover:text-neutral-200 ${isScrolled ? 'text-neutral-dark' : 'text-white'}`}>
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {cartCount > 0 && (
              <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                {cartCount}
              </span>
            )}
          </Link>

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
          <Link to="/marketplace" className="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">Marketplace</Link>
          <Link to="/cart" className="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">
            Cart {cartCount > 0 && `(${cartCount})`}
          </Link>
          <a href="/login" className="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">Login</a>
          <a href="/register" className="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">Become a HelpMate</a>
        </div>
      )}
    </header>
  );
};

export default Navbar;
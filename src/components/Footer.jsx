import React from 'react';
import { Link } from 'react-router-dom'; // <--- ADD THIS LINE
const Footer = () => {
  return (
    <footer className="bg-neutral-darkest text-white border-t border-neutral-dark">
        <div className="container mx-auto px-6 py-12 max-w-7xl">
            {/* Main Footer Grid */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-12">
                
                {/* Column 1: Brand & Logo */}
                <div className="col-span-1 md:col-span-1">
                    <a href="#" className="flex items-center gap-3 mb-4">
                        {/* Added w-10 to force width and prevent collapse */}
                        <img src="/img/logo2.png" alt="ShareStrength" className="h-10 w-auto object-contain" />
                    </a>
                    <p className="text-neutral-medium text-sm leading-relaxed">
                        Your independence, supported. connecting you to trusted help for everyday tasks.
                    </p>
                </div>

                {/* Column 2: Platform */}
                <div>
                    <h5 className="font-bold tracking-wider uppercase mb-6 text-neutral-400 text-xs">Platform</h5>
                    <div className="flex flex-col gap-3">
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">How it Works</a>
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">Find Help</a>
                        <Link to="/register-helpmate" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">Become a HelpMate</Link>
                    </div>
                </div>

                {/* Column 3: Company */}
                <div>
                    <h5 className="font-bold tracking-wider uppercase mb-6 text-neutral-400 text-xs">Company</h5>
                    <div className="flex flex-col gap-3">
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">About Us</a>
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">Careers</a>
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">Press</a>
                    </div>
                </div>

                 {/* Column 4: Support */}
                 <div>
                    <h5 className="font-bold tracking-wider uppercase mb-6 text-neutral-400 text-xs">Support</h5>
                    <div className="flex flex-col gap-3">
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">Help Center</a>
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">Trust & Safety</a>
                        <a href="#" className="text-neutral-300 hover:text-primary-light transition-colors text-sm">Contact Us</a>
                    </div>
                </div>
            </div>

            {/* Copyright Bar */}
            <div className="mt-16 pt-8 border-t border-neutral-800 text-center">
                <p className="text-neutral-500 text-sm">&copy; 2025 ShareStrength. All rights reserved.</p>
            </div>
        </div>
    </footer>
  );
};

export default Footer;
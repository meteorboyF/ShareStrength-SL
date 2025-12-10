import React, { useEffect } from 'react';
import Navbar from './components/Navbar';
import HowItWorks from './components/HowItWorks';

// Simple functional component for Hero
const Hero = () => (
  <section className="relative bg-cover bg-center pt-32 pb-20 md:pt-48 md:pb-32 text-center" style={{ backgroundImage: "url('/img/indexbg.jpg')" }}>
    <div className="absolute inset-0 bg-black/50"></div>
    <div className="container mx-auto px-6 max-w-4xl relative z-10">
        <h1 className="text-4xl md:text-6xl font-extrabold text-white mb-6">Reliable Help, Right at Your Fingertips.</h1>
        <p className="text-lg md:text-xl text-neutral-light max-w-2xl mx-auto mb-10">
            ShareStrength is the trusted platform connecting individuals with disabilities to a community of helpers.
        </p>
        <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button className="bg-primary text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-primary-dark transition">Find a HelpMate</button>
        </div>
    </div>
  </section>
);

function App() {
  // Intersection Observer Logic for animations
  useEffect(() => {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
        }
      });
    });

    document.querySelectorAll('.will-animate').forEach((el) => observer.observe(el));
  }, []);

  return (
    <div className="font-sans text-neutral-dark antialiased">
      <Navbar />
      <main>
        <Hero />
        {/* You can add the Features/Challenge section component here */}
        <HowItWorks />
        {/* Eye Tracking Section would go here */}
      </main>
      
      {/* Simple Footer */}
      <footer className="bg-neutral-darkest text-white py-12 text-center">
        <p>&copy; 2025 ShareStrength. All rights reserved.</p>
      </footer>
    </div>
  );
}

export default App;
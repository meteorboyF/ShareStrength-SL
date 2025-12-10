import React from 'react';

const Hero = () => {
  return (
    <section className="relative bg-cover bg-center pt-32 pb-20 md:pt-48 md:pb-32 text-center overflow-hidden" style={{ backgroundImage: "url('/img/indexbg.jpg')" }}>
        <div className="absolute inset-0 bg-black/50"></div>
        
        <div className="container mx-auto px-6 max-w-4xl relative z-10">
            <h1 className="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight will-animate">Reliable Help, Right at Your Fingertips.</h1>
            <p className="text-lg md:text-xl text-neutral-light max-w-2xl mx-auto mb-10 will-animate" style={{ transitionDelay: '150ms' }}>
                ShareStrength is the trusted platform connecting individuals with disabilities to a community of vetted, skilled, and compassionate helpers for everyday tasks and specialized support.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center will-animate" style={{ transitionDelay: '300ms' }}>
                <a href="/registercustomer" className="bg-primary text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-primary-dark hover:-translate-y-1 transform transition">Find a HelpMate Today</a>
                <a href="#how-it-works" className="bg-white/90 text-primary-dark font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-white hover:-translate-y-1 transform border border-primary/20 transition">Learn How It Works</a>
            </div>
        </div>
    </section>
  );
};

export default Hero;
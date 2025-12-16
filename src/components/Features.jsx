import React from 'react';

const features = [
  {
    title: "Uncertainty and Safety",
    desc: "Inviting someone new requires trust. We make it easy to verify who is qualified, reliable, and safe.",
    icon: (
      <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    )
  },
  {
    title: "Finding the Right Skills",
    desc: "Your needs are unique. Our platform helps you find someone with specific skills without the hassle.",
    icon: (
      <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
    )
  },
  {
    title: "Inflexible Arrangements",
    desc: "Traditional agencies can be rigid. Here, you get the flexibility to find help on your schedule.",
    icon: (
      <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
    )
  }
];

const Features = () => {
  return (
    <section className="py-20 md:py-28 bg-white">
        <div className="container mx-auto px-6 max-w-7xl text-center">
            <p className="text-sm font-bold text-primary uppercase tracking-wider mb-3 will-animate">THE CHALLENGE</p>
            <h2 className="text-3xl md:text-4xl font-extrabold text-neutral-darkest mb-16 will-animate" style={{ transitionDelay: '150ms' }}>Finding Trustworthy Help Shouldn't Be Hard</h2>
            
            <div className="grid md:grid-cols-3 gap-8 text-left">
                {features.map((feature, index) => (
                    <div key={index} className="bg-neutral-light p-8 rounded-2xl will-animate" style={{ transitionDelay: `${300 + (index * 150)}ms` }}>
                        <div className="mb-5 inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                             {feature.icon}
                        </div>
                        <h3 className="text-xl font-bold text-neutral-darkest mb-3">{feature.title}</h3>
                        <p className="text-neutral-medium leading-relaxed">{feature.desc}</p>
                    </div>
                ))}
            </div>
        </div>
    </section>
  );
};

export default Features;
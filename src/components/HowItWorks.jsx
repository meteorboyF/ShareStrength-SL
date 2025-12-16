import React, { useState } from 'react';

const stepsData = [
  { id: 1, title: "1. Post Your Task", desc: "Describe what you need...", img: "https://placehold.co/600x450/6D28D9/FFFFFF?text=1.+Post+Your+Task" },
  { id: 2, title: "2. Connect", desc: "Receive applications...", img: "https://placehold.co/600x450/5B21B6/FFFFFF?text=2.+Connect" },
  { id: 3, title: "3. Get Support", desc: "Coordinate with your HelpMate...", img: "https://placehold.co/600x450/4C1D95/FFFFFF?text=3.+Get+Support" },
  { id: 4, title: "4. Pay Securely", desc: "Approve payment securely...", img: "https://placehold.co/600x450/3730A3/FFFFFF?text=4.+Pay+Securely" },
];

const HowItWorks = () => {
  const [activeStep, setActiveStep] = useState(stepsData[0]);

  return (
    <section id="how-it-works" className="py-20 md:py-28 bg-neutral-light">
      <div className="container mx-auto px-6 max-w-7xl">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-extrabold text-neutral-darkest">Get Support in 4 Simple Steps</h2>
        </div>

        <div className="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">
          {/* Dynamic Image Area */}
          <div className="lg:sticky top-28 h-96 lg:h-[30rem]">
            <div 
              className="w-full h-full rounded-2xl shadow-2xl bg-cover bg-center transition-all duration-500"
              style={{ backgroundImage: `url('${activeStep.img}')` }}
            ></div>
          </div>

          {/* Steps List */}
          <div className="flex flex-col gap-6">
            {stepsData.map((step) => (
              <div 
                key={step.id}
                onClick={() => setActiveStep(step)}
                className={`p-6 rounded-xl cursor-pointer border-2 transition-all duration-300 ${
                  activeStep.id === step.id 
                    ? 'bg-white border-primary/50 shadow-lg' 
                    : 'border-transparent hover:bg-white/50'
                }`}
              >
                <div className="flex items-start gap-5">
                  <div className="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-full bg-primary/10 text-primary">
                    {/* You can insert specific SVGs here based on step.id */}
                    <span className="font-bold">{step.id}</span>
                  </div>
                  <div>
                    <h4 className="font-bold text-lg text-neutral-darkest mb-1">{step.title}</h4>
                    <p className="text-neutral-medium">{step.desc}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default HowItWorks;
import React, { useState } from 'react';
import { ClipboardEdit, Users, HeartHandshake, ShieldCheck } from 'lucide-react';

const stepsData = [
  { 
    id: 1, 
    title: "1. Post Your Task", 
    desc: "Describe what you need help with. Whether it's grocery shopping, reading assistance, or mobility support.", 
    // Pointing to your local folder: public/img/step1-post.jpg
    img: "/img/step1-post.jpg", 
    icon: ClipboardEdit 
  },
  { 
    id: 2, 
    title: "2. Connect", 
    desc: "Verified HelpMates in your area will see your request. Review their profiles and ratings before accepting.", 
    img: "/img/step2-connect.jpg", 
    icon: Users 
  },
  { 
    id: 3, 
    title: "3. Get Support", 
    desc: "Coordinate with your HelpMate. They arrive to assist you with care, respect, and efficiency.", 
    img: "/img/step3-support.jpg", 
    icon: HeartHandshake 
  },
  { 
    id: 4, 
    title: "4. Pay Securely", 
    desc: "Payment is held securely and only released once the task is completed to your satisfaction.", 
    img: "/img/step4-pay.jpg", 
    icon: ShieldCheck 
  },
];

const HowItWorks = () => {
  const [activeStep, setActiveStep] = useState(stepsData[0]);

  return (
    <section id="how-it-works" className="py-20 md:py-28 bg-gray-50">
      <div className="container mx-auto px-6 max-w-7xl">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-extrabold text-gray-900">
            Get Support in 4 Simple Steps
          </h2>
          <p className="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
            Our platform is designed to be fully accessible, making it safe and easy to find the exact help you need.
          </p>
        </div>

        <div className="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">
          
          {/* LEFT SIDE: Dynamic Image Area with Cross-Fade */}
          <div className="lg:sticky top-28 h-96 lg:h-[30rem] relative rounded-2xl overflow-hidden shadow-2xl bg-gray-200">
            {stepsData.map((step) => (
              <img
                key={step.id}
                src={step.img}
                alt={step.title}
                className={`absolute inset-0 w-full h-full object-cover transition-opacity duration-700 ease-in-out ${
                  activeStep.id === step.id ? 'opacity-100 z-10' : 'opacity-0 z-0'
                }`}
                // If image fails to load, log it to console to help debug
                onError={(e) => {
                    console.error(`Image failed to load: ${step.img}`);
                    e.target.style.display = 'none';
                }}
              />
            ))}
          </div>

          {/* RIGHT SIDE: Steps List */}
          <div className="flex flex-col gap-6">
            {stepsData.map((step) => {
              const IconComponent = step.icon;
              const isActive = activeStep.id === step.id;

              return (
                <div 
                  key={step.id}
                  onClick={() => setActiveStep(step)}
                  className={`p-6 rounded-xl cursor-pointer border-2 transition-all duration-300 ${
                    isActive 
                      ? 'bg-white border-purple-600 shadow-xl scale-105' 
                      : 'border-transparent bg-white/50 hover:bg-white hover:shadow-md'
                  }`}
                >
                  <div className="flex items-start gap-5">
                    <div className={`flex-shrink-0 h-14 w-14 flex items-center justify-center rounded-full transition-colors duration-300 ${
                      isActive ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-600'
                    }`}>
                      <IconComponent className="w-7 h-7" />
                    </div>
                    <div>
                      <h4 className={`font-bold text-xl mb-2 ${
                        isActive ? 'text-purple-900' : 'text-gray-900'
                      }`}>
                        {step.title}
                      </h4>
                      <p className={`leading-relaxed ${
                        isActive ? 'text-gray-700' : 'text-gray-500'
                      }`}>
                        {step.desc}
                      </p>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
          
        </div>
      </div>
    </section>
  );
};

export default HowItWorks;
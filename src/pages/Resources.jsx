import React, { useState } from 'react';
import { Link } from 'react-router-dom';

// --- MOCK DATA (Pointing to your local files) ---
const RESOURCES = [
  { 
    id: 1, 
    title: "Indian vs American Sign Language", 
    category: "video", 
    description: "A comparison video showing the differences between ISL and ASL.",
    // MAKE SURE THIS FILENAME MATCHES EXACTLY
    link: "/resources/WhatsApp Video 2025-05-22 at 23.06.17_bcfc1ec3.mp4" 
  },
  { 
    id: 2, 
    title: "Sign Language: 'How are you?'", 
    category: "video", 
    description: "Learn the common greeting 'How are you?' in sign language.",
    // MAKE SURE THIS FILENAME MATCHES EXACTLY
    link: "/resources/WhatsApp Video 2025-05-22 at 23.06.32_40364811.mp4" 
  },
  { 
    id: 3, 
    title: "Under the Guns (Audio)", 
    category: "audio", 
    description: "Audio discussion and podcast episode.",
    // MAKE SURE THIS FILENAME MATCHES EXACTLY
    link: "/resources/undertheguns_00_wittenmyer_64kb.mp3" 
  },
  { 
    id: 4, 
    title: "Accessibility Guidelines PDF", 
    category: "tutorial", 
    description: "Standard guidelines for web accessibility (Placeholder).",
    link: "https://placehold.co/600x400/png?text=PDF+Placeholder" // Placeholder for now
  }
];

const Resources = () => {
  const [filter, setFilter] = useState('all');
  const [selectedResource, setSelectedResource] = useState(null);

  // Filter Logic
  const filteredResources = RESOURCES.filter(resource => 
    filter === 'all' || resource.category === filter
  );

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-6 lg:p-8">
      <div className="max-w-7xl mx-auto space-y-8">
        
        {/* Header */}
        <header className="text-center animate-fade-in-up">
            <div className="flex justify-start">
                 <Link to="/dashboard" className="text-primary hover:text-primary-dark text-sm font-semibold mb-2 inline-block">
                    &larr; Back to Dashboard
                </Link>
            </div>
            <h1 className="text-3xl font-extrabold text-neutral-darkest">Resource Library</h1>
            <p className="mt-2 text-neutral-medium max-w-2xl mx-auto">
                A curated collection of tutorials, videos, and audio guides to support your journey.
            </p>
        </header>

        {/* Filters */}
        <div className="flex justify-center gap-2 animate-fade-in-up" style={{animationDelay: '100ms'}}>
            {['all', 'video', 'audio', 'tutorial'].map((type) => (
                <button
                    key={type}
                    onClick={() => setFilter(type)}
                    className={`px-4 py-2 rounded-full text-sm font-bold capitalize transition shadow-sm ${
                        filter === type 
                        ? 'bg-primary text-white ring-2 ring-primary ring-offset-2' 
                        : 'bg-white text-neutral-dark hover:bg-neutral-50 border border-neutral-200'
                    }`}
                >
                    {type}
                </button>
            ))}
        </div>

        {/* Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up" style={{animationDelay: '200ms'}}>
            {filteredResources.map((resource) => (
                <div key={resource.id} className="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition group">
                    <div className="p-6 flex-grow">
                        <div className="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center mb-4 text-primary">
                            {/* Icons based on category */}
                            {resource.category === 'video' && (
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            )}
                            {resource.category === 'audio' && (
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                            )}
                            {resource.category === 'tutorial' && (
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            )}
                        </div>
                        <span className="text-xs font-bold uppercase tracking-wider text-primary mb-2 block">{resource.category}</span>
                        <h3 className="text-lg font-bold text-neutral-darkest mb-2 group-hover:text-primary transition">{resource.title}</h3>
                        <p className="text-sm text-neutral-medium line-clamp-3">{resource.description}</p>
                    </div>
                    <div className="px-6 py-4 bg-neutral-50 border-t border-neutral-100">
                        <button 
                            onClick={() => setSelectedResource(resource)}
                            className="w-full bg-primary text-white text-sm font-bold py-2 rounded-lg hover:bg-primary-dark transition shadow-sm"
                        >
                            View Resource
                        </button>
                    </div>
                </div>
            ))}
        </div>

      </div>

      {/* --- Media Viewer Modal --- */}
      {selectedResource && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in-up">
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
                <div className="p-4 border-b border-neutral-200 flex justify-between items-center bg-white">
                    <h3 className="font-bold text-lg text-neutral-darkest">{selectedResource.title}</h3>
                    <button onClick={() => setSelectedResource(null)} className="p-1 rounded-full hover:bg-neutral-100">
                        <svg className="w-6 h-6 text-neutral-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div className="p-6 bg-black flex items-center justify-center flex-grow overflow-auto">
                    {selectedResource.category === 'video' && (
                        <video controls autoPlay className="max-w-full max-h-[70vh] rounded-lg shadow-lg">
                            <source src={selectedResource.link} type="video/mp4" />
                            Your browser does not support the video tag.
                        </video>
                    )}

                    {selectedResource.category === 'audio' && (
                        <div className="w-full max-w-md bg-neutral-800 p-8 rounded-xl flex flex-col items-center gap-4">
                            <div className="w-20 h-20 bg-primary rounded-full flex items-center justify-center text-white animate-pulse">
                                <svg className="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                            </div>
                            <p className="text-white font-medium">{selectedResource.title}</p>
                            <audio controls autoPlay className="w-full">
                                <source src={selectedResource.link} type="audio/mpeg" />
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    )}

                    {selectedResource.category === 'tutorial' && (
                         <img src={selectedResource.link} alt="Tutorial" className="max-w-full max-h-[70vh] object-contain" />
                    )}
                </div>
            </div>
        </div>
      )}
    </div>
  );
};

export default Resources;
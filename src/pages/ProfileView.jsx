import React from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';

// --- MOCK DATA ---
const PROFILES = {
  helpmate: [
    { 
      id: 5, 
      name: "John Doe", 
      role: "HelpMate", 
      photo: "https://placehold.co/150", 
      location: "Brooklyn, NY",
      rating: 4.8, 
      jobs_completed: 124, 
      skills: ["Mobility Support", "Housekeeping", "Cooking"],
      bio: "Compassionate and experienced HelpMate with over 3 years of experience in elderly care and mobility support. I love cooking healthy meals and good conversation.",
      reviews: [
        { id: 1, user: "Alice M.", rating: 5, text: "John was incredibly helpful and kind. Highly recommended!" },
        { id: 2, user: "Robert F.", rating: 4, text: "Great help with the heavy lifting." }
      ]
    },
    { 
      id: 6, 
      name: "Jane Roe", 
      role: "HelpMate", 
      photo: "https://placehold.co/150", 
      location: "Manhattan, NY",
      rating: 4.5, 
      jobs_completed: 45, 
      skills: ["Tech Support", "Reading Assistance"],
      bio: "Patient and tech-savvy. I specialize in helping seniors navigate modern technology and providing reading assistance.",
      reviews: [
        { id: 3, user: "Sarah C.", rating: 5, text: "Jane fixed my iPad in minutes!" }
      ]
    }
  ],
  user: [
    { 
      id: 101, 
      name: "Mr. Fredricksen", 
      role: "User", 
      photo: "https://placehold.co/150", 
      location: "Queens, NY",
      joined: "Jan 2024",
      bio: "Retired teacher looking for assistance with grocery shopping and occasional tech support.",
      reviews: [] // Users might not have public reviews in this version
    },
    {
      id: 501, // Alice M. from Dashboard mock
      name: "Alice M.",
      role: "User",
      photo: "https://placehold.co/150",
      location: "Brooklyn, NY",
      joined: "Mar 2024",
      bio: "Looking for a walking companion for morning exercises.",
      reviews: []
    }
  ]
};

const ProfileView = () => {
  const { type, id } = useParams(); // URL will be /profile/helpmate/5 or /profile/user/101
  const navigate = useNavigate();

  // Find the profile
  const profileList = PROFILES[type] || [];
  const profile = profileList.find(p => p.id === parseInt(id));

  if (!profile) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-slate-50">
        <h2 className="text-2xl font-bold text-slate-800">Profile Not Found</h2>
        <button onClick={() => navigate(-1)} className="mt-4 text-indigo-600 hover:underline">Go Back</button>
      </div>
    );
  }

  const isHelpMate = type === 'helpmate';

  return (
    <div className="min-h-screen bg-slate-100 font-sans p-4 sm:p-8">
      <div className="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        
        {/* Header / Cover */}
        <div className="h-32 bg-gradient-to-r from-indigo-500 to-purple-600 relative">
          <button 
            onClick={() => navigate(-1)}
            className="absolute top-4 left-4 bg-white/20 hover:bg-white/40 text-white p-2 rounded-full backdrop-blur-sm transition"
          >
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          </button>
        </div>

        {/* Profile Info */}
        <div className="px-8 pb-8">
          <div className="relative flex justify-between items-end -mt-12 mb-6">
            <img 
              src={profile.photo} 
              alt={profile.name} 
              className="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white shadow-md object-cover bg-white"
            />
            <div className="flex gap-3 mb-2">
              <button className="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-sm transition">
                Message
              </button>
              {isHelpMate && (
                <button className="px-4 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-sm transition">
                  Hire Now
                </button>
              )}
            </div>
          </div>

          <div>
            <h1 className="text-3xl font-extrabold text-slate-900 flex items-center gap-2">
              {profile.name}
              {isHelpMate && <span className="text-blue-500" title="Verified HelpMate"><svg className="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg></span>}
            </h1>
            <p className="text-slate-500 font-medium flex items-center gap-1 mt-1">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              {profile.location}
            </p>
          </div>

          {/* Stats Row */}
          {isHelpMate && (
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 py-6 border-y border-slate-100">
              <div>
                <span className="block text-2xl font-bold text-slate-900">{profile.rating} ★</span>
                <span className="text-xs text-slate-500 uppercase tracking-wide">Rating</span>
              </div>
              <div>
                <span className="block text-2xl font-bold text-slate-900">{profile.jobs_completed}</span>
                <span className="text-xs text-slate-500 uppercase tracking-wide">Jobs Done</span>
              </div>
              <div className="col-span-2 sm:col-span-2">
                <span className="block text-sm font-bold text-slate-900 mb-1">Skills</span>
                <div className="flex flex-wrap gap-1">
                  {profile.skills.map(skill => (
                    <span key={skill} className="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-md border border-indigo-100">{skill}</span>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* Bio */}
          <div className="mt-8">
            <h3 className="text-lg font-bold text-slate-900 mb-3">About</h3>
            <p className="text-slate-600 leading-relaxed">
              {profile.bio}
            </p>
          </div>

          {/* Reviews (Only for HelpMates) */}
          {isHelpMate && (
            <div className="mt-8">
              <h3 className="text-lg font-bold text-slate-900 mb-4">Reviews</h3>
              {profile.reviews.length > 0 ? (
                <div className="space-y-4">
                  {profile.reviews.map(review => (
                    <div key={review.id} className="bg-slate-50 p-4 rounded-xl border border-slate-100">
                      <div className="flex justify-between items-start mb-2">
                        <span className="font-bold text-slate-800">{review.user}</span>
                        <span className="text-yellow-500 text-sm font-bold">{'★'.repeat(review.rating)}</span>
                      </div>
                      <p className="text-slate-600 text-sm">"{review.text}"</p>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-slate-500 italic">No reviews yet.</p>
              )}
            </div>
          )}

        </div>
      </div>
    </div>
  );
};

export default ProfileView;
import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import api from '../services/api';
import authService from '../services/authService';

const ProfileView = () => {
  const { type, id } = useParams(); // URL will be /profile/user/5 or /profile/helpmate/5
  const navigate = useNavigate();
  const [profile, setProfile] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const currentUser = authService.getCurrentUser();
  const isOwnProfile = currentUser && currentUser.id === parseInt(id);

  useEffect(() => {
    const fetchProfile = async () => {
      try {
        setLoading(true);
        const response = await api.get(`/users/${id}`);
        setProfile(response.data);
      } catch (err) {
        console.error('Error fetching profile:', err);
        setError('Failed to load profile');
      } finally {
        setLoading(false);
      }
    };

    fetchProfile();
  }, [id]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
          <p className="mt-4 text-slate-600">Loading profile...</p>
        </div>
      </div>
    );
  }

  if (error || !profile) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-slate-50">
        <h2 className="text-2xl font-bold text-slate-800">Profile Not Found</h2>
        <p className="text-slate-600 mt-2">{error}</p>
        <button onClick={() => navigate(-1)} className="mt-4 text-indigo-600 hover:underline">Go Back</button>
      </div>
    );
  }

  const isHelper = profile.role === 'caregiver';

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
              src={profile.profile_photo_url || "https://placehold.co/150"}
              alt={profile.name}
              className="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white shadow-md object-cover bg-white"
            />
            <div className="flex gap-3 mb-2">
              {isOwnProfile ? (
                <Link
                  to="/profile/edit"
                  className="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-sm transition"
                >
                  Edit Profile
                </Link>
              ) : (
                <>
                  <button className="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    Message
                  </button>
                  {isHelper && (
                    <button className="px-4 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-sm transition">
                      Hire Now
                    </button>
                  )}
                </>
              )}
            </div>
          </div>

          <div>
            <h1 className="text-3xl font-extrabold text-slate-900 flex items-center gap-2">
              {profile.name}
              {isHelper && <span className="text-blue-500" title="Verified HelpMate"><svg className="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" /></svg></span>}
            </h1>
            <p className="text-slate-500 font-medium flex items-center gap-1 mt-1">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              {profile.location || 'Location not set'}
            </p>
            <p className="text-sm text-slate-500 mt-1">
              {profile.email} {profile.phone && `• ${profile.phone}`}
            </p>
          </div>

          {/* Stats Row */}
          {isHelper && (
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 py-6 border-y border-slate-100">
              <div>
                <span className="block text-2xl font-bold text-slate-900">{profile.rating || 0} ★</span>
                <span className="text-xs text-slate-500 uppercase tracking-wide">Rating</span>
              </div>
              <div>
                <span className="block text-2xl font-bold text-slate-900">{profile.completed_jobs || 0}</span>
                <span className="text-xs text-slate-500 uppercase tracking-wide">Jobs Done</span>
              </div>
              <div className="col-span-2 sm:col-span-2">
                <span className="block text-sm font-bold text-slate-900 mb-1">Skills</span>
                <div className="flex flex-wrap gap-1">
                  {profile.skills ? profile.skills.split(',').map(skill => (
                    <span key={skill.trim()} className="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-md border border-indigo-100">{skill.trim()}</span>
                  )) : <span className="text-xs text-slate-400">No skills listed</span>}
                </div>
              </div>
            </div>
          )}

          {!isHelper && profile.disability_type && (
            <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
              <p className="text-sm text-blue-800"><strong>Disability Type:</strong> {profile.disability_type}</p>
            </div>
          )}

          {/* Bio */}
          <div className="mt-8">
            <h3 className="text-lg font-bold text-slate-900 mb-3">About</h3>
            <p className="text-slate-600 leading-relaxed">
              {profile.bio || 'No bio provided yet.'}
            </p>
          </div>

          {/* Member Since */}
          <div className="mt-6 text-sm text-slate-500">
            Member since {new Date(profile.created_at).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}
          </div>

        </div>
      </div>
    </div>
  );
};

export default ProfileView;
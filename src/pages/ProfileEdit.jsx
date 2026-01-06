import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import authService from '../services/authService';

const ProfileEdit = () => {
    const navigate = useNavigate();
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [user, setUser] = useState(null);
    const [photo, setPhoto] = useState(null);
    const [photoPreview, setPhotoPreview] = useState(null);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        location: '',
        bio: '',
        disability_type: '',
        skills: ''
    });

    useEffect(() => {
        const fetchUser = async () => {
            try {
                const response = await api.get('/me');
                const userData = response.data;
                setUser(userData);
                setFormData({
                    name: userData.name || '',
                    email: userData.email || '',
                    phone: userData.phone || '',
                    location: userData.location || '',
                    bio: userData.bio || '',
                    disability_type: userData.disability_type || '',
                    skills: userData.skills || ''
                });
            } catch (err) {
                console.error('Error fetching user:', err);
                alert('Failed to load profile data');
                navigate(-1);
            } finally {
                setLoading(false);
            }
        };

        fetchUser();
    }, [navigate]);

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handlePhotoChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setPhoto(file);
            // Create preview URL
            const reader = new FileReader();
            reader.onloadend = () => {
                setPhotoPreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);

        try {
            // Upload photo first if selected
            if (photo) {
                const photoFormData = new FormData();
                photoFormData.append('photo', photo);
                await api.post('/profile/photo', photoFormData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
            }

            // Then update profile data
            await api.put('/profile', formData);

            // Update localStorage with new user data
            const response = await api.get('/me');
            localStorage.setItem('user', JSON.stringify(response.data));

            alert('Profile updated successfully!');
            navigate(`/profile/${user.role === 'caregiver' ? 'helpmate' : 'user'}/${user.id}`);
        } catch (err) {
            console.error('Error updating profile:', err);
            alert('Failed to update profile: ' + (err.response?.data?.message || err.message));
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-slate-50">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                    <p className="mt-4 text-slate-600">Loading...</p>
                </div>
            </div>
        );
    }

    const isHelper = user?.role === 'caregiver';

    return (
        <div className="min-h-screen bg-slate-100 font-sans p-4 sm:p-8">
            <div className="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl p-8">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-3xl font-bold text-slate-900">Edit Profile</h1>
                    <button
                        onClick={() => navigate(-1)}
                        className="text-slate-600 hover:text-slate-900"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Profile Photo */}
                    <div className="flex flex-col items-center pb-6 border-b border-slate-200">
                        <div className="relative mb-4">
                            <img
                                src={photoPreview || user?.profile_photo_url || "https://placehold.co/150"}
                                alt="Profile"
                                className="w-32 h-32 rounded-full object-cover border-4 border-indigo-100"
                            />
                            {photoPreview && (
                                <div className="absolute -top-2 -right-2 bg-green-500 text-white rounded-full p-1">
                                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                    </svg>
                                </div>
                            )}
                        </div>
                        <label className="cursor-pointer bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg hover:bg-indigo-100 transition font-semibold text-sm">
                            <input
                                type="file"
                                accept="image/*"
                                onChange={handlePhotoChange}
                                className="hidden"
                            />
                            {photoPreview ? 'Change Photo' : 'Upload Photo'}
                        </label>
                        {photoPreview && (
                            <p className="text-xs text-green-600 mt-2">New photo selected - click Save to upload</p>
                        )}
                    </div>

                    {/* Name */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-2">
                            Full Name *
                        </label>
                        <input
                            type="text"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            required
                            className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    {/* Email */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-2">
                            Email *
                        </label>
                        <input
                            type="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            required
                            className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    {/* Phone */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-2">
                            Phone
                        </label>
                        <input
                            type="tel"
                            name="phone"
                            value={formData.phone}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    {/* Location */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-2">
                            Location
                        </label>
                        <input
                            type="text"
                            name="location"
                            value={formData.location}
                            onChange={handleChange}
                            placeholder="City, State"
                            className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    {/* Disability Type (PWD only) */}
                    {!isHelper && (
                        <div>
                            <label className="block text-sm font-semibold text-slate-700 mb-2">
                                Disability Type
                            </label>
                            <input
                                type="text"
                                name="disability_type"
                                value={formData.disability_type}
                                onChange={handleChange}
                                placeholder="e.g., Mobility, Visual, Hearing"
                                className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                    )}

                    {/* Skills (Helper only) */}
                    {isHelper && (
                        <div>
                            <label className="block text-sm font-semibold text-slate-700 mb-2">
                                Skills (comma-separated)
                            </label>
                            <input
                                type="text"
                                name="skills"
                                value={formData.skills}
                                onChange={handleChange}
                                placeholder="e.g., Mobility Support, Cooking, Housekeeping"
                                className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                            <p className="text-xs text-slate-500 mt-1">Separate multiple skills with commas</p>
                        </div>
                    )}

                    {/* Bio */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-2">
                            Bio
                        </label>
                        <textarea
                            name="bio"
                            value={formData.bio}
                            onChange={handleChange}
                            rows="4"
                            maxLength="1000"
                            placeholder="Tell us about yourself..."
                            className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                        />
                        <p className="text-xs text-slate-500 mt-1">{formData.bio.length}/1000 characters</p>
                    </div>

                    {/* Buttons */}
                    <div className="flex gap-4 pt-4">
                        <button
                            type="submit"
                            disabled={saving}
                            className="flex-1 bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {saving ? 'Saving...' : 'Save Changes'}
                        </button>
                        <button
                            type="button"
                            onClick={() => navigate(-1)}
                            className="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-lg hover:bg-slate-300 transition"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default ProfileEdit;

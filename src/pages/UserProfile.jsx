import React, { useState, useEffect } from 'react'; // Fixed duplicates if any, utilizing existing logic
import { Link, useNavigate } from 'react-router-dom';
import api from '../services/api';
import { User, Mail, Phone, MapPin, Edit2, Save, X, ArrowLeft, Camera, FileText, Globe } from 'lucide-react';

const UserProfile = () => {
    const navigate = useNavigate();
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [isEditing, setIsEditing] = useState(false);
    const [formData, setFormData] = useState({});
    const [message, setMessage] = useState({ type: '', text: '' });

    // Image Upload State
    const [selectedImage, setSelectedImage] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);
    const fileInputRef = React.useRef(null);

    useEffect(() => {
        fetchUser();
    }, []);

    const fetchUser = async () => {
        try {
            const response = await api.get('/me');
            setUser(response.data);
            setFormData(response.data);
            localStorage.setItem('user', JSON.stringify(response.data));
        } catch (error) {
            console.error('Failed to fetch user:', error);
            setMessage({ type: 'error', text: 'Failed to load profile data.' });
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setSelectedImage(file);
            setPreviewImage(URL.createObjectURL(file));
        }
    };

    const handleEditClick = () => {
        setIsEditing(true);
    };

    const handleCancelClick = () => {
        setIsEditing(false);
        setFormData(user);
        setSelectedImage(null);
        setPreviewImage(null);
        setMessage({ type: '', text: '' });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setMessage({ type: '', text: '' });

        try {
            const submitData = new FormData();
            submitData.append('_method', 'PUT'); // Spoof PUT for Laravel to handle file upload

            // Append text fields from formData
            Object.keys(formData).forEach(key => {
                // Skip profile_photo string from backend, and other non-updatable fields if desired
                if (key === 'profile_photo') return;
                if (formData[key] !== null && formData[key] !== undefined) {
                    submitData.append(key, formData[key]);
                }
            });

            // Append new image if selected
            if (selectedImage) {
                submitData.append('profile_photo', selectedImage);
            }

            const response = await api.post('/me', submitData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            setUser(response.data.user);
            setFormData(response.data.user);
            setIsEditing(false);
            setSelectedImage(null);
            setPreviewImage(null);
            setMessage({ type: 'success', text: 'Profile updated successfully!' });
            localStorage.setItem('user', JSON.stringify(response.data.user));
        } catch (error) {
            console.error('Update failed:', error);
            const errorMsg = error.response?.data?.message || 'Failed to update profile.';
            setMessage({ type: 'error', text: errorMsg });
        }
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        );
    }

    if (!user) {
        return (
            <div className="min-h-screen flex flex-col items-center justify-center bg-gray-50">
                <p className="text-red-500 mb-4">Error loading profile.</p>
                <Link to="/dashboard" className="text-primary hover:underline">Return to Dashboard</Link>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-neutral-light font-sans text-neutral-dark pb-12">
            {/* Header */}
            <header className="bg-white shadow-sm sticky top-0 z-40">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button
                        onClick={() => navigate(-1)}
                        className="flex items-center gap-2 text-neutral-medium hover:text-primary transition"
                    >
                        <ArrowLeft size={20} />
                        <span className="font-medium">Back</span>
                    </button>
                    <h1 className="text-xl font-bold text-neutral-darkest">My Profile</h1>
                    <div className="w-16"></div> {/* Spacer for centering */}
                </div>
            </header>

            <main className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">

                {/* Notification Banner */}
                {message.text && (
                    <div className={`mb-6 p-4 rounded-lg flex items-center justify-between ${message.type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'}`}>
                        <span>{message.text}</span>
                        <button onClick={() => setMessage({ type: '', text: '' })}><X size={18} /></button>
                    </div>
                )}

                <div className="bg-white rounded-2xl shadow-xl overflow-hidden border border-neutral-100">

                    {/* Cover / Header Section */}
                    <div className="bg-gradient-to-r from-primary/10 to-purple-100 h-32 sm:h-48 relative">
                        <div className="absolute -bottom-12 left-8">
                            <div className="relative group">
                                <img
                                    src={previewImage || user.profile_photo || user.profile_photo_url || "https://placehold.co/150x150"}
                                    alt="Profile"
                                    className="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white object-cover shadow-md bg-white"
                                />
                                {isEditing && (
                                    <>
                                        <button
                                            onClick={() => fileInputRef.current.click()}
                                            className="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow border border-gray-200 text-gray-600 hover:text-primary transition hover:shadow-md"
                                            title="Change Profile Photo"
                                        >
                                            <Camera size={16} />
                                        </button>
                                        <input
                                            type="file"
                                            ref={fileInputRef}
                                            onChange={handleImageChange}
                                            accept="image/*"
                                            className="hidden"
                                        />
                                    </>
                                )}
                            </div>
                        </div>
                        <div className="absolute top-4 right-4">
                            {!isEditing ? (
                                <button
                                    onClick={handleEditClick}
                                    className="flex items-center gap-2 bg-white text-neutral-dark px-4 py-2 rounded-lg shadow-sm hover:shadow transition font-medium text-sm"
                                >
                                    <Edit2 size={16} /> Edit Profile
                                </button>
                            ) : (
                                <button
                                    onClick={handleCancelClick}
                                    className="flex items-center gap-2 bg-white text-red-600 px-4 py-2 rounded-lg shadow-sm hover:shadow transition font-medium text-sm"
                                >
                                    <X size={16} /> Cancel
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="pt-16 pb-8 px-8">
                        <div className="mb-6">
                            <h2 className="text-2xl font-bold text-neutral-darkest">{user.name}</h2>
                            <p className="text-neutral-medium capitalize">{user.role ? user.role.replace('_', ' ') : 'User'}</p>
                            <span className={`inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold ${user.verification_status === 'verified' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
                                {user.verification_status || 'Unverified Account'}
                            </span>
                        </div>

                        {isEditing ? (
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-neutral-700 flex items-center gap-2">
                                            <User size={16} /> Full Name
                                        </label>
                                        <input
                                            type="text"
                                            name="name"
                                            value={formData.name || ''}
                                            onChange={handleChange}
                                            className="w-full px-4 py-2 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                            required
                                        />
                                    </div>

                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-neutral-700 flex items-center gap-2">
                                            <Mail size={16} /> Email Address
                                        </label>
                                        <input
                                            type="email"
                                            name="email"
                                            value={formData.email || ''}
                                            disabled
                                            className="w-full px-4 py-2 rounded-lg border border-neutral-200 bg-gray-50 text-gray-500 cursor-not-allowed"
                                        />
                                        <p className="text-xs text-gray-400">Email cannot be changed directly.</p>
                                    </div>

                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-neutral-700 flex items-center gap-2">
                                            <Phone size={16} /> Phone Number
                                        </label>
                                        <input
                                            type="tel"
                                            name="phone" // Changed from phone_number to phone
                                            value={formData.phone || ''}
                                            onChange={handleChange}
                                            placeholder="+1 234 567 8900"
                                            className="w-full px-4 py-2 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                        />
                                    </div>

                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-neutral-700 flex items-center gap-2">
                                            <Globe size={16} /> Location (City, Country)
                                        </label>
                                        <input
                                            type="text"
                                            name="location"
                                            value={formData.location || ''}
                                            onChange={handleChange}
                                            placeholder="e.g. New York, USA"
                                            className="w-full px-4 py-2 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                        />
                                    </div>

                                    <div className="space-y-2 md:col-span-2">
                                        <label className="text-sm font-semibold text-neutral-700 flex items-center gap-2">
                                            <MapPin size={16} /> Full Address
                                        </label>
                                        <textarea
                                            name="address"
                                            value={formData.address || ''}
                                            onChange={handleChange}
                                            rows="2"
                                            className="w-full px-4 py-2 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"
                                        />
                                    </div>

                                    <div className="space-y-2 md:col-span-2">
                                        <label className="text-sm font-semibold text-neutral-700 flex items-center gap-2">
                                            <FileText size={16} /> Bio / About Me
                                        </label>
                                        <textarea
                                            name="bio"
                                            value={formData.bio || ''}
                                            onChange={handleChange}
                                            rows="4"
                                            placeholder="Tell us a bit about yourself..."
                                            className="w-full px-4 py-2 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"
                                        />
                                    </div>

                                    {/* Helper Specific Fields */}
                                    {user.role === 'caregiver' && (
                                        <div className="space-y-2 md:col-span-2">
                                            <label className="text-sm font-semibold text-neutral-700">Skills & Qualifications</label>
                                            <textarea
                                                name="skills"
                                                value={formData.skills || ''}
                                                onChange={handleChange}
                                                rows="3"
                                                className="w-full px-4 py-2 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"
                                            />
                                        </div>
                                    )}
                                </div>

                                <div className="flex justify-end gap-4 pt-4 border-t border-neutral-100">
                                    <button
                                        type="button"
                                        onClick={() => { setIsEditing(false); setFormData(user); }}
                                        className="px-6 py-2 rounded-lg text-neutral-600 hover:bg-neutral-50 transition font-medium"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        className="px-6 py-2 rounded-lg bg-primary text-white hover:bg-primary-dark shadow-md hover:shadow-lg transition font-medium flex items-center gap-2"
                                    >
                                        <Save size={18} /> Save Changes
                                    </button>
                                </div>
                            </form>
                        ) : (
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div className="space-y-6">
                                    <div>
                                        <h3 className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Contact Information</h3>
                                        <div className="space-y-3">
                                            <div className="flex items-center gap-3 text-neutral-700">
                                                <div className="bg-blue-50 p-2 rounded-full text-blue-600"><Mail size={16} /></div>
                                                <span>{user.email}</span>
                                            </div>
                                            <div className="flex items-center gap-3 text-neutral-700">
                                                <div className="bg-green-50 p-2 rounded-full text-green-600"><Phone size={16} /></div>
                                                <span>{user.phone || 'No phone number added'}</span>
                                            </div>
                                            <div className="flex items-center gap-3 text-neutral-700">
                                                <div className="bg-purple-50 p-2 rounded-full text-purple-600"><Globe size={16} /></div>
                                                <span>{user.location || 'No location set'}</span>
                                            </div>
                                            <div className="flex items-start gap-3 text-neutral-700">
                                                <div className="bg-red-50 p-2 rounded-full text-red-600 mt-1"><MapPin size={16} /></div>
                                                <span className="leading-relaxed">{user.address || 'No address added'}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h3 className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Account Details</h3>
                                        <div className="bg-neutral-50 p-4 rounded-lg border border-neutral-100 space-y-2">
                                            <div className="flex justify-between text-sm">
                                                <span className="text-neutral-500">Member Since</span>
                                                <span className="font-medium text-neutral-800">{new Date(user.created_at).toLocaleDateString()}</span>
                                            </div>
                                            <div className="flex justify-between text-sm">
                                                <span className="text-neutral-500">Account Type</span>
                                                <span className="font-medium text-neutral-800 capitalize">{user.role || 'Standard User'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-6">
                                    <div>
                                        <h3 className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">About ({user.name})</h3>
                                        <div className="text-neutral-700 leading-relaxed whitespace-pre-line">
                                            {user.bio || 'No bio provided. Click edit to add a bio.'}
                                        </div>
                                    </div>

                                    {user.role === 'caregiver' && (
                                        <div>
                                            <h3 className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Skills & Services</h3>
                                            <div className="bg-neutral-50 p-4 rounded-lg border border-neutral-100 text-neutral-700 whitespace-pre-line">
                                                {user.skills || 'No skills listed yet.'}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </main>
        </div>
    );
};

export default UserProfile;

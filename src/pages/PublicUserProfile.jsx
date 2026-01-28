import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import api from '../services/api';
import { User, Mail, Phone, MapPin, ArrowLeft, Users, Shield } from 'lucide-react';

const PublicUserProfile = () => {
    const { userId } = useParams();
    const navigate = useNavigate();
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetchUserProfile();
    }, [userId]);

    const fetchUserProfile = async () => {
        try {
            const response = await api.get(`/users/${userId}`);
            setUser(response.data);
        } catch (err) {
            console.error('Failed to fetch user profile:', err);
            setError('Failed to load user profile');
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        );
    }

    if (error || !user) {
        return (
            <div className="min-h-screen flex flex-col items-center justify-center bg-gray-50">
                <p className="text-red-500 mb-4">{error || 'User not found'}</p>
                <button onClick={() => navigate(-1)} className="text-primary hover:underline">
                    Go Back
                </button>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-neutral-light font-sans text-neutral-dark pb-12">
            {/* Header */}
            <header className="bg-white shadow-sm sticky top-0 z-40">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button
                        onClick={() => navigate(-1)}
                        className="flex items-center gap-2 text-neutral-medium hover:text-primary transition"
                    >
                        <ArrowLeft size={20} />
                        <span className="font-medium">Back</span>
                    </button>
                    <h1 className="text-xl font-bold text-neutral-darkest">User Profile</h1>
                    <div className="w-16"></div> {/* Spacer */}
                </div>
            </header>

            <main className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">

                <div className="bg-white rounded-2xl shadow-xl overflow-hidden border border-neutral-100">

                    {/* Cover / Header Section */}
                    <div className="bg-gradient-to-r from-primary/10 to-purple-100 h-32 sm:h-40 relative">
                        <div className="absolute -bottom-12 left-8">
                            <img
                                src={user.profile_photo || user.profile_photo_url || "https://placehold.co/150x150"}
                                alt={user.name}
                                className="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white object-cover shadow-md bg-white"
                            />
                        </div>
                    </div>

                    <div className="pt-16 pb-8 px-8">
                        <div className="mb-6">
                            <h2 className="text-2xl font-bold text-neutral-darkest">{user.name}</h2>
                            <p className="text-neutral-medium">Person with Disability (PWD)</p>
                            <div className="mt-3 flex gap-2">
                                <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                    <User size={14} />
                                    User
                                </span>
                                {user.disability_type && (
                                    <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700">
                                        {user.disability_type}
                                    </span>
                                )}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {/* Contact Information */}
                            <div className="space-y-6">
                                <div>
                                    <h3 className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-3">Contact Information</h3>
                                    <div className="space-y-3">
                                        <div className="flex items-center gap-3 text-neutral-700">
                                            <div className="bg-blue-50 p-2 rounded-full text-blue-600">
                                                <Mail size={16} />
                                            </div>
                                            <span>{user.email}</span>
                                        </div>
                                        {(user.phone || user.phone_number) && (
                                            <div className="flex items-center gap-3 text-neutral-700">
                                                <div className="bg-green-50 p-2 rounded-full text-green-600">
                                                    <Phone size={16} />
                                                </div>
                                                <span>{user.phone || user.phone_number}</span>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                {user.address && (
                                    <div>
                                        <h3 className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-3">Location</h3>
                                        <div className="flex items-start gap-3 text-neutral-700">
                                            <div className="bg-red-50 p-2 rounded-full text-red-600 mt-1">
                                                <MapPin size={16} />
                                            </div>
                                            <span className="leading-relaxed">{user.address}</span>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Account Details */}
                            <div>
                                <h3 className="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-3">Account Details</h3>
                                <div className="bg-neutral-50 p-4 rounded-lg border border-neutral-100 space-y-2">
                                    <div className="flex justify-between text-sm">
                                        <span className="text-neutral-500">Member Since</span>
                                        <span className="font-medium text-neutral-800">
                                            {new Date(user.created_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Trusted Contacts Section */}
                        {user.trusted_contacts && user.trusted_contacts.length > 0 && (
                            <div className="mt-8 pt-8 border-t border-neutral-200">
                                <div className="flex items-center gap-2 mb-4">
                                    <Shield className="text-green-600" size={20} />
                                    <h3 className="text-lg font-bold text-neutral-darkest">Trusted Contacts</h3>
                                </div>
                                <p className="text-sm text-neutral-600 mb-4">
                                    Emergency contacts and trusted individuals who can be reached if needed.
                                </p>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {user.trusted_contacts.map((contact) => (
                                        <div
                                            key={contact.id}
                                            className="bg-green-50 border border-green-200 rounded-lg p-4 hover:shadow-md transition"
                                        >
                                            <div className="flex items-start gap-3">
                                                <div className="bg-green-100 p-2 rounded-full text-green-700">
                                                    <Users size={18} />
                                                </div>
                                                <div className="flex-1">
                                                    <h4 className="font-bold text-sm text-neutral-darkest">
                                                        {contact.contact_name}
                                                    </h4>
                                                    {contact.relation && (
                                                        <p className="text-xs text-neutral-600 mb-2">
                                                            {contact.relation}
                                                        </p>
                                                    )}
                                                    <div className="space-y-1">
                                                        {contact.contact_email && (
                                                            <div className="flex items-center gap-2 text-xs text-neutral-700">
                                                                <Mail size={12} />
                                                                <span>{contact.contact_email}</span>
                                                            </div>
                                                        )}
                                                        {contact.contact_phone && (
                                                            <div className="flex items-center gap-2 text-xs text-neutral-700">
                                                                <Phone size={12} />
                                                                <span>{contact.contact_phone}</span>
                                                            </div>
                                                        )}
                                                    </div>
                                                    {contact.status && (
                                                        <span className={`inline-block mt-2 px-2 py-0.5 rounded-full text-xs font-semibold ${contact.status === 'verified'
                                                            ? 'bg-green-100 text-green-700'
                                                            : 'bg-yellow-100 text-yellow-700'
                                                            }`}>
                                                            {contact.status}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* No trusted contacts message */}
                        {(!user.trusted_contacts || user.trusted_contacts.length === 0) && (
                            <div className="mt-8 pt-8 border-t border-neutral-200">
                                <div className="flex items-center gap-2 mb-2">
                                    <Shield className="text-neutral-400" size={20} />
                                    <h3 className="text-lg font-bold text-neutral-darkest">Trusted Contacts</h3>
                                </div>
                                <p className="text-sm text-neutral-500">
                                    This user has not added any trusted contacts yet.
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </main>
        </div>
    );
};

export default PublicUserProfile;

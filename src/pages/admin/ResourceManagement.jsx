import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import { ArrowLeft, Upload, File, Trash2, Search } from 'lucide-react';

const ResourceManagement = () => {
    const navigate = useNavigate();
    const [resources, setResources] = useState([]);
    const [loading, setLoading] = useState(true);
    const [uploading, setUploading] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');

    const [formData, setFormData] = useState({ title: '', description: '', file: null });

    useEffect(() => {
        fetchResources();
    }, [searchTerm]);

    const fetchResources = async () => {
        try {
            const params = {};
            if (searchTerm) params.search = searchTerm;
            const response = await api.get('/admin/resources', { params });
            setResources(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch resources:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleUpload = async (e) => {
        e.preventDefault();
        if (!formData.file || !formData.title) return alert('Please fill all fields');

        setUploading(true);
        try {
            const data = new FormData();
            data.append('file', formData.file);
            data.append('title', formData.title);
            data.append('description', formData.description);

            await api.post('/admin/resources/upload', data, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            alert('Resource uploaded successfully');
            setFormData({ title: '', description: '', file: null });
            fetchResources();
        } catch (error) {
            alert('Upload failed');
        } finally {
            setUploading(false);
        }
    };

    const handleDelete = async (resourceId) => {
        if (!confirm('Delete this resource?')) return;
        try {
            await api.delete(`/admin/resources/${resourceId}/delete`);
            alert('Resource deleted');
            fetchResources();
        } catch (error) {
            alert('Delete failed');
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 pb-12">
            <header className="bg-white shadow-sm border-b">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button onClick={() => navigate(-1)} className="flex items-center gap-2 text-slate-600 hover:text-primary">
                        <ArrowLeft size={20} />
                        Back
                    </button>
                    <div className="flex items-center gap-2">
                        <File className="text-blue-600" size={24} />
                        <h1 className="text-xl font-bold">Resource Management</h1>
                    </div>
                    <div className="w-16"></div>
                </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Upload Form */}
                <div className="bg-white rounded-xl p-6 shadow-sm border mb-6">
                    <h2 className="text-lg font-bold mb-4">Upload New Resource</h2>
                    <form onSubmit={handleUpload} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium mb-2">Title</label>
                            <input type="text" value={formData.title}
                                onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                                className="w-full px-4 py-2 border rounded-lg" required />
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-2">Description</label>
                            <textarea value={formData.description}
                                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                className="w-full px-4 py-2 border rounded-lg" rows="3" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-2">File (PDF, DOC, Images max 10MB)</label>
                            <input type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
                                onChange={(e) => setFormData({ ...formData, file: e.target.files[0] })}
                                className="w-full px-4 py-2 border rounded-lg" required />
                        </div>
                        <button type="submit" disabled={uploading}
                            className="flex items-center gap-2 px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark disabled:opacity-50">
                            <Upload size={18} />
                            {uploading ? 'Uploading...' : 'Upload Resource'}
                        </button>
                    </form>
                </div>

                {/* Search */}
                <div className="bg-white rounded-xl p-6 shadow-sm border mb-6">
                    <div className="relative">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" size={20} />
                        <input type="text" placeholder="Search resources..." value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="w-full pl-10 pr-4 py-2 border rounded-lg" />
                    </div>
                </div>

                {/* Resources List */}
                <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
                    {loading ? (
                        <div className="flex justify-center items-center py-12">
                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                        </div>
                    ) : (
                        <table className="w-full">
                            <thead className="bg-slate-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">ID</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Title</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Description</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">File Type</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Size</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {resources.length === 0 ? (
                                    <tr><td colSpan="6" className="px-6 py-12 text-center text-slate-500">No resources found</td></tr>
                                ) : (
                                    resources.map((resource) => (
                                        <tr key={resource.id} className="hover:bg-slate-50">
                                            <td className="px-6 py-4 text-sm">{resource.id}</td>
                                            <td className="px-6 py-4 text-sm font-medium">{resource.title}</td>
                                            <td className="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">{resource.description || 'N/A'}</td>
                                            <td className="px-6 py-4 text-sm uppercase">{resource.file_type || 'N/A'}</td>
                                            <td className="px-6 py-4 text-sm">{resource.file_size ? `${(resource.file_size / 1024).toFixed(2)} KB` : 'N/A'}</td>
                                            <td className="px-6 py-4">
                                                <button onClick={() => handleDelete(resource.id)}
                                                    className="flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 text-xs font-medium">
                                                    <Trash2 size={14} />
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    )}
                </div>
            </main>
        </div>
    );
};

export default ResourceManagement;

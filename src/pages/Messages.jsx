import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Search, MoreVertical } from 'lucide-react';
import ConversationListItem from '../components/ConversationListItem';
import ChatWindow from '../components/ChatWindow';
import { getConversations } from '../services/api';

const Messages = () => {
    const { conversationId } = useParams();
    const navigate = useNavigate();
    const [conversations, setConversations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentUserId, setCurrentUserId] = useState(null);
    const [currentUserType, setCurrentUserType] = useState(null);
    const [searchQuery, setSearchQuery] = useState('');
    const [isMobileView, setIsMobileView] = useState(false);

    useEffect(() => {
        // Get current user from localStorage
        const user = JSON.parse(localStorage.getItem('user') || '{}');

        // Determine type based on role (set in AuthController)
        // role 'caregiver' -> helper, 'pwd'/'family_member' -> user
        const type = user.role === 'caregiver' ? 'helper' : 'user';
        setCurrentUserType(type);

        // Get the correct ID based on user type
        const userId = type === 'helper' ? user.helper_id : user.user_id;
        setCurrentUserId(userId);

        fetchConversations();
    }, []);

    const fetchConversations = async () => {
        try {
            setLoading(true);
            const response = await getConversations();
            setConversations(response.data || []);
        } catch (error) {
            console.error('Failed to fetch conversations:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleConversationClick = (id) => {
        navigate(`/messages/${id}`);
        setIsMobileView(true);
    };

    const handleBack = () => {
        setIsMobileView(false);
        navigate('/messages');
    };

    // Filter conversations based on search
    const filteredConversations = conversations.filter(c =>
        c.other_user?.name?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    return (
        <div className="flex h-screen bg-slate-50 text-slate-900 font-sans antialiased overflow-hidden">

            {/* LEFT PANEL: Conversation List */}
            <aside className={`
                ${conversationId && isMobileView ? 'hidden' : 'flex'} 
                md:flex flex-col w-full md:w-96 bg-white border-r border-slate-200 shadow-sm transition-all duration-300
            `}>
                {/* Sidebar Header */}
                <div className="p-6 pb-4">
                    <div className="flex items-center justify-between mb-6">
                        <h1 className="text-2xl font-bold tracking-tight text-slate-800">Messages</h1>
                        <button className="p-2 hover:bg-slate-100 rounded-full transition-colors">
                            <MoreVertical size={20} className="text-slate-500" />
                        </button>
                    </div>

                    {/* Search Bar */}
                    <div className="relative group">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors" size={18} />
                        <input
                            type="text"
                            placeholder="Search conversations..."
                            className="w-full pl-10 pr-4 py-2.5 bg-slate-100 border-transparent border focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 rounded-xl outline-none transition-all text-sm"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>
                </div>

                {/* Conversation Items */}
                <div className="flex-1 overflow-y-auto px-3 space-y-1">
                    {loading ? (
                        <div className="flex items-center justify-center h-full">
                            <div className="text-center">
                                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                                <p className="mt-4 text-gray-600">Loading conversations...</p>
                            </div>
                        </div>
                    ) : filteredConversations.length === 0 ? (
                        <div className="flex items-center justify-center h-full px-6">
                            <div className="text-center">
                                <svg
                                    className="mx-auto h-20 w-20 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={1}
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                    />
                                </svg>
                                <h3 className="mt-4 text-lg font-medium text-gray-900">No conversations yet</h3>
                                <p className="mt-2 text-sm text-gray-500">
                                    Start a conversation by messaging someone from a task or their profile.
                                </p>
                            </div>
                        </div>
                    ) : (
                        filteredConversations.map((conversation) => (
                            <ConversationListItem
                                key={conversation.id}
                                conversation={conversation}
                                isActive={conversation.id === parseInt(conversationId)}
                                onClick={() => handleConversationClick(conversation.id)}
                            />
                        ))
                    )}
                </div>
            </aside>

            {/* RIGHT PANEL: Chat Window */}
            <main className={`
                ${!conversationId || !isMobileView ? 'hidden' : 'flex'} 
                md:flex flex-1 flex-col bg-white overflow-hidden
            `}>
                <ChatWindow
                    conversationId={conversationId ? parseInt(conversationId) : null}
                    currentUserId={currentUserId}
                    currentUserType={currentUserType}
                    onBack={handleBack}
                />
            </main>

        </div>
    );
};

export default Messages;

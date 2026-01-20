import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
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

    useEffect(() => {
        // Get current user from localStorage
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        setCurrentUserId(user.id || user.user_id || user.helper_id); // robust id check

        // Determine type based on role (set in AuthController)
        // role 'caregiver' -> helper, 'pwd'/'family_member' -> user
        const type = user.role === 'caregiver' ? 'helper' : 'user';
        setCurrentUserType(type);

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
    };

    return (
        <div className="flex h-screen bg-gray-100">
            {/* Conversations List - Left Panel */}
            <div className="w-full md:w-96 bg-white border-r border-gray-200 flex flex-col">
                {/* Header */}
                <div className="border-b border-gray-200 bg-white px-6 py-4 shadow-sm">
                    <h1 className="text-2xl font-bold text-gray-900">Messages</h1>
                </div>

                {/* Conversations List */}
                <div className="flex-1 overflow-y-auto">
                    {loading ? (
                        <div className="flex items-center justify-center h-full">
                            <div className="text-center">
                                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                                <p className="mt-4 text-gray-600">Loading conversations...</p>
                            </div>
                        </div>
                    ) : conversations.length === 0 ? (
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
                        conversations.map((conversation) => (
                            <ConversationListItem
                                key={conversation.id}
                                conversation={conversation}
                                isActive={conversation.id === parseInt(conversationId)}
                                onClick={() => handleConversationClick(conversation.id)}
                            />
                        ))
                    )}
                </div>
            </div>

            {/* Chat Window - Right Panel */}
            <div className="flex-1 hidden md:flex">
                <ChatWindow
                    conversationId={conversationId ? parseInt(conversationId) : null}
                    currentUserId={currentUserId}
                    currentUserType={currentUserType}
                />
            </div>

            {/* Mobile: Show chat window as overlay when conversation is selected */}
            {conversationId && (
                <div className="fixed inset-0 bg-white z-50 md:hidden">
                    <div className="h-full flex flex-col">
                        <div className="bg-white border-b border-gray-200 px-4 py-3 flex items-center">
                            <button
                                onClick={() => navigate('/messages')}
                                className="mr-4 p-2 hover:bg-gray-100 rounded-full"
                            >
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <h2 className="text-lg font-semibold">Chat</h2>
                        </div>
                        <div className="flex-1">
                            <ChatWindow
                                conversationId={parseInt(conversationId)}
                                currentUserId={currentUserId}
                                currentUserType={currentUserType}
                            />
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Messages;

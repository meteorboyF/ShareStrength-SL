import React, { useState, useEffect, useRef } from 'react';
import MessageBubble from './MessageBubble';
import MessageInput from './MessageInput';
import { getConversation, sendMessage, markConversationAsRead } from '../services/api';

const ChatWindow = ({ conversationId, currentUserId, currentUserType }) => {
    const [messages, setMessages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [sending, setSending] = useState(false);
    const [conversation, setConversation] = useState(null);
    const messagesEndRef = useRef(null);
    const pollIntervalRef = useRef(null);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    const fetchMessages = async (showLoading = true) => {
        try {
            if (showLoading) setLoading(true);
            const response = await getConversation(conversationId);
            setConversation(response.data.conversation);
            setMessages(response.data.messages.data || []);

            // Mark conversation as read
            await markConversationAsRead(conversationId);
        } catch (error) {
            console.error('Failed to fetch messages:', error);
        } finally {
            if (showLoading) setLoading(false);
        }
    };

    const handleSendMessage = async (content) => {
        if (!conversation) return;

        setSending(true);
        try {
            const response = await sendMessage({
                receiver_id: conversation.other_user.id,
                receiver_type: conversation.other_user.type, // Pass helper/user type
                task_id: conversation.task?.id || null,
                content,
            });

            // Add new message to the list
            setMessages(prev => [...prev, response.data]);
            scrollToBottom();
        } catch (error) {
            console.error('Failed to send message:', error);
            alert('Failed to send message. Please try again.');
        } finally {
            setSending(false);
        }
    };

    useEffect(() => {
        if (conversationId) {
            fetchMessages();

            // Poll for new messages every 3 seconds
            pollIntervalRef.current = setInterval(() => {
                fetchMessages(false);
            }, 3000);
        }

        return () => {
            if (pollIntervalRef.current) {
                clearInterval(pollIntervalRef.current);
            }
        };
    }, [conversationId]);

    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    if (!conversationId) {
        return (
            <div className="flex items-center justify-center h-full bg-gray-50">
                <div className="text-center">
                    <svg
                        className="mx-auto h-24 w-24 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={1}
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                        />
                    </svg>
                    <h3 className="mt-4 text-lg font-medium text-gray-900">Select a conversation</h3>
                    <p className="mt-1 text-sm text-gray-500">
                        Choose a conversation from the list to start messaging
                    </p>
                </div>
            </div>
        );
    }

    if (loading) {
        return (
            <div className="flex items-center justify-center h-full">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p className="mt-4 text-gray-600">Loading messages...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="flex flex-col h-full bg-white">
            {/* Chat Header */}
            {conversation && (
                <div className="border-b border-gray-200 bg-white px-6 py-4 shadow-sm">
                    <div className="flex items-center">
                        <div>
                            <h2 className="text-lg font-semibold text-gray-900">
                                {conversation.other_user.name}
                            </h2>
                            <p className="text-sm text-gray-500 capitalize">
                                {conversation.other_user.role}
                            </p>
                        </div>
                        {conversation.task && (
                            <div className="ml-auto">
                                <span className="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-full">
                                    Task: {conversation.task.title}
                                </span>
                            </div>
                        )}
                    </div>
                </div>
            )}

            {/* Messages List */}
            <div className="flex-1 overflow-y-auto px-6 py-4 bg-gray-50">
                {messages.length === 0 ? (
                    <div className="flex items-center justify-center h-full">
                        <p className="text-gray-500">No messages yet. Start the conversation!</p>
                    </div>
                ) : (
                    <>
                        {messages.map((message) => (
                            <MessageBubble
                                key={message.id}
                                message={message}
                                isOwnMessage={message.sender_id === currentUserId && message.sender_type === currentUserType}
                            />
                        ))}
                        <div ref={messagesEndRef} />
                    </>
                )}
            </div>

            {/* Message Input */}
            <MessageInput onSend={handleSendMessage} disabled={sending} />
        </div>
    );
};

export default ChatWindow;

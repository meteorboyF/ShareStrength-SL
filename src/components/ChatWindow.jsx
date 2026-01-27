import React, { useState, useEffect, useRef } from 'react';
import { Home, Paperclip, Smile, Send, ChevronLeft } from 'lucide-react';
import MessageBubble from './MessageBubble';
import { getConversation, sendMessage, markConversationAsRead } from '../services/api';
import { useNavigate } from 'react-router-dom';

const ChatWindow = ({ conversationId, currentUserId, currentUserType, onBack }) => {
    const navigate = useNavigate();
    const [messages, setMessages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [sending, setSending] = useState(false);
    const [conversation, setConversation] = useState(null);
    const [newMessage, setNewMessage] = useState('');
    const messagesEndRef = useRef(null);
    const scrollRef = useRef(null);
    const pollIntervalRef = useRef(null);
    const textareaRef = useRef(null);

    const scrollToBottom = () => {
        if (scrollRef.current) {
            scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
        }
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

    const handleSendMessage = async (e) => {
        e.preventDefault();
        if (!conversation || !newMessage.trim()) return;

        setSending(true);
        try {
            await sendMessage({
                receiver_id: conversation.other_user.id,
                receiver_type: conversation.other_user.type,
                task_id: conversation.task?.task_id || null,
                content: newMessage.trim(),
            });

            setNewMessage('');
            // Refresh messages to get the properly formatted data
            await fetchMessages(false);
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

    // Auto-resize textarea
    useEffect(() => {
        if (textareaRef.current) {
            textareaRef.current.style.height = 'auto';
            textareaRef.current.style.height = Math.min(textareaRef.current.scrollHeight, 128) + 'px';
        }
    }, [newMessage]);

    if (!conversationId) {
        return (
            <div className="flex-1 flex flex-col items-center justify-center p-8 bg-slate-50 text-center">
                <div className="w-24 h-24 bg-white rounded-3xl shadow-xl flex items-center justify-center mb-6 animate-bounce">
                    <Send size={40} className="text-blue-500 -rotate-12" />
                </div>
                <h3 className="text-xl font-bold text-slate-800">Your Secure Messenger</h3>
                <p className="text-slate-500 mt-2 max-w-sm leading-relaxed">
                    Select a conversation to start messaging. All conversations are end-to-end encrypted for your safety.
                </p>
            </div>
        );
    }

    if (loading) {
        return (
            <div className="flex items-center justify-center h-full">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p className="mt-4 text-slate-600">Loading messages...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="flex flex-col h-full bg-white">
            {/* Chat Header */}
            {conversation && (
                <header className="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white/80 backdrop-blur-md sticky top-0 z-10">
                    <div className="flex items-center gap-4">
                        <button
                            onClick={onBack}
                            className="md:hidden p-2 hover:bg-slate-100 rounded-lg transition-colors"
                        >
                            <ChevronLeft size={20} />
                        </button>
                        <div className="relative">
                            {conversation.other_user.profile_photo ? (
                                <img
                                    src={conversation.other_user.profile_photo}
                                    className="w-10 h-10 rounded-full object-cover"
                                    alt={conversation.other_user.name}
                                />
                            ) : (
                                <div className="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {conversation.other_user.name?.charAt(0)}
                                </div>
                            )}
                            {/* Online indicator */}
                            <div className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full" />
                        </div>
                        <div>
                            <h2 className="font-bold text-slate-800 leading-tight">{conversation.other_user.name}</h2>
                            <p className="text-xs text-slate-400 font-medium capitalize">{conversation.other_user.role}</p>
                        </div>
                    </div>

                    <button
                        onClick={() => navigate('/dashboard')}
                        className="flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all font-medium"
                        title="Back to Dashboard"
                    >
                        <Home size={18} />
                        <span>Return to Home</span>
                    </button>
                </header>
            )}

            {/* Messages Area */}
            <div
                ref={scrollRef}
                className="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50/50"
            >
                {messages.length === 0 ? (
                    <div className="flex items-center justify-center h-full">
                        <p className="text-slate-500">No messages yet. Start the conversation!</p>
                    </div>
                ) : (
                    <>
                        {messages.map((message) => (
                            <MessageBubble
                                key={message.id}
                                message={message}
                                isOwnMessage={parseInt(message.sender_id) === parseInt(currentUserId) && message.sender_type === currentUserType}
                            />
                        ))}
                        <div ref={messagesEndRef} />
                    </>
                )}
            </div>

            {/* Input Area */}
            <footer className="p-4 bg-white border-t border-slate-100">
                <form
                    onSubmit={handleSendMessage}
                    className="max-w-4xl mx-auto flex items-end gap-2 bg-slate-50 border border-slate-200 rounded-2xl p-2 focus-within:ring-2 focus-within:ring-blue-100 focus-within:border-blue-300 transition-all"
                >
                    <div className="flex items-center gap-1">
                        <button type="button" className="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-all">
                            <Paperclip size={20} />
                        </button>
                        <button type="button" className="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-all">
                            <Smile size={20} />
                        </button>
                    </div>

                    <textarea
                        ref={textareaRef}
                        rows="1"
                        placeholder="Type a message..."
                        className="flex-1 bg-transparent border-none outline-none text-sm py-2 px-1 resize-none max-h-32 min-h-[40px]"
                        value={newMessage}
                        onChange={(e) => setNewMessage(e.target.value)}
                        onKeyDown={(e) => {
                            if (e.key === 'Enter' && !e.shiftKey) {
                                e.preventDefault();
                                handleSendMessage(e);
                            }
                        }}
                        disabled={sending}
                    />

                    <button
                        type="submit"
                        disabled={!newMessage.trim() || sending}
                        className={`
                            p-3 rounded-xl transition-all shadow-md active:scale-95
                            ${newMessage.trim() && !sending
                                ? 'bg-blue-600 text-white shadow-blue-200 hover:bg-blue-700'
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed'}
                        `}
                    >
                        <Send size={18} />
                    </button>
                </form>
            </footer>
        </div>
    );
};

export default ChatWindow;

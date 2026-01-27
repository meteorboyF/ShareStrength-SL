import React from 'react';
import { CheckCheck } from 'lucide-react';

const MessageBubble = ({ message, isOwnMessage }) => {
    const formatTime = (timestamp) => {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInHours = (now - date) / (1000 * 60 * 60);

        if (diffInHours < 24) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
    };

    return (
        <div className={`flex ${isOwnMessage ? 'justify-end' : 'justify-start'} group animate-in fade-in slide-in-from-bottom-2 duration-300`}>
            <div className="max-w-[80%] md:max-w-[65%] space-y-1">
                {!isOwnMessage && (
                    <p className="text-sm font-medium text-slate-700 mb-1">
                        {message.sender?.name}
                    </p>
                )}
                <div
                    className={`p-4 rounded-2xl text-sm leading-relaxed shadow-sm ${isOwnMessage
                            ? 'bg-blue-600 text-white rounded-tr-none'
                            : 'bg-gray-100 text-gray-800 rounded-tl-none'
                        }`}
                >
                    <p className="whitespace-pre-wrap break-words">{message.content}</p>
                </div>
                <div className={`flex items-center gap-1.5 px-1 ${isOwnMessage ? 'justify-end' : 'justify-start'}`}>
                    <span className="text-[10px] font-medium text-slate-400">{formatTime(message.created_at)}</span>
                    {isOwnMessage && (
                        <CheckCheck size={12} className={message.read_at ? 'text-blue-500' : 'text-slate-300'} />
                    )}
                </div>
            </div>
        </div>
    );
};

export default MessageBubble;

import React from 'react';

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
        <div className={`flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-4`}>
            <div className={`max-w-[70%] ${isOwnMessage ? 'order-2' : 'order-1'}`}>
                {!isOwnMessage && (
                    <p className="text-xs text-gray-500 mb-1 ml-3">
                        {message.sender?.name}
                    </p>
                )}
                <div
                    className={`rounded-2xl px-4 py-2 shadow-sm ${isOwnMessage
                            ? 'bg-blue-600 text-white rounded-tr-sm'
                            : 'bg-gray-100 text-gray-800 rounded-tl-sm'
                        }`}
                >
                    <p className="text-sm whitespace-pre-wrap break-words">{message.content}</p>
                </div>
                <p className={`text-xs text-gray-400 mt-1 ${isOwnMessage ? 'text-right mr-3' : 'ml-3'}`}>
                    {formatTime(message.created_at)}
                    {isOwnMessage && message.is_read && ' · Read'}
                </p>
            </div>
        </div>
    );
};

export default MessageBubble;

import React from 'react';

const ConversationListItem = ({ conversation, onClick, isActive }) => {
    const formatTime = (timestamp) => {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diffInHours = (now - date) / (1000 * 60 * 60);

        if (diffInHours < 24) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else if (diffInHours < 48) {
            return 'Yesterday';
        }
        return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
    };

    const getInitials = (name) => {
        if (!name) return '?';
        return name
            .split(' ')
            .map(word => word[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    };

    return (
        <div
            onClick={onClick}
            className={`flex items-center p-4 cursor-pointer border-b border-gray-200 hover:bg-gray-50 transition-colors ${isActive ? 'bg-blue-50 border-l-4 border-l-blue-600' : ''
                }`}
        >
            {/* Avatar */}
            <div className="flex-shrink-0 mr-3">
                {conversation.other_user.profile_photo ? (
                    <img
                        src={conversation.other_user.profile_photo}
                        alt={conversation.other_user.name}
                        className="w-12 h-12 rounded-full object-cover"
                    />
                ) : (
                    <div className="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                        {getInitials(conversation.other_user.name)}
                    </div>
                )}
            </div>

            {/* Content */}
            <div className="flex-1 min-w-0">
                <div className="flex items-center justify-between mb-1">
                    <h3 className="text-sm font-semibold text-gray-900 truncate">
                        {conversation.other_user.name}
                    </h3>
                    {conversation.last_message_at && (
                        <span className="text-xs text-gray-500 ml-2 flex-shrink-0">
                            {formatTime(conversation.last_message_at)}
                        </span>
                    )}
                </div>

                {/* Last Message Preview */}
                {conversation.last_message && (
                    <p className="text-sm text-gray-600 truncate">
                        {conversation.last_message.is_from_me && 'You: '}
                        {conversation.last_message.content}
                    </p>
                )}

                {/* Task Badge */}
                {conversation.task && (
                    <span className="inline-block mt-1 px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">
                        Task: {conversation.task.title}
                    </span>
                )}
            </div>

            {/* Unread Badge */}
            {conversation.unread_count > 0 && (
                <div className="flex-shrink-0 ml-2">
                    <span className="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-blue-600 rounded-full">
                        {conversation.unread_count > 9 ? '9+' : conversation.unread_count}
                    </span>
                </div>
            )}
        </div>
    );
};

export default ConversationListItem;

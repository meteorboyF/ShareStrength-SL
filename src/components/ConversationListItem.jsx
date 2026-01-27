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
        <button
            onClick={onClick}
            className={`
                w-full flex items-start gap-3 p-4 rounded-2xl transition-all duration-200 group
                ${isActive
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-200'
                    : 'hover:bg-slate-50 text-slate-600'}
            `}
        >
            <div className="relative flex-shrink-0">
                {conversation.other_user.profile_photo ? (
                    <img
                        src={conversation.other_user.profile_photo}
                        alt={conversation.other_user.name}
                        className="w-12 h-12 rounded-full object-cover ring-2 ring-transparent group-hover:ring-slate-200 transition-all"
                    />
                ) : (
                    <div className="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold ring-2 ring-transparent group-hover:ring-slate-200 transition-all">
                        {getInitials(conversation.other_user.name)}
                    </div>
                )}
                {/* Online indicator - you can add online status to your API later */}
                <div className={`absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full border-2 ${isActive ? 'border-blue-600 bg-green-400' : 'border-white bg-green-400'}`} />
            </div>

            <div className="flex-1 text-left min-w-0">
                <div className="flex justify-between items-center mb-0.5">
                    <h3 className={`font-semibold truncate ${isActive ? 'text-white' : 'text-slate-900'}`}>
                        {conversation.other_user.name}
                    </h3>
                    <span className={`text-[11px] font-medium ${isActive ? 'text-blue-100' : 'text-slate-400'}`}>
                        {formatTime(conversation.last_message_at)}
                    </span>
                </div>
                <div className="flex justify-between items-center gap-2">
                    <p className={`text-xs truncate ${isActive ? 'text-blue-50' : 'text-slate-500'}`}>
                        {conversation.last_message?.is_from_me && 'You: '}
                        {conversation.last_message?.content || 'No messages yet'}
                    </p>
                    {conversation.unread_count > 0 && !isActive && (
                        <span className="bg-blue-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
                            {conversation.unread_count > 9 ? '9+' : conversation.unread_count}
                        </span>
                    )}
                </div>
            </div>
        </button>
    );
};

export default ConversationListItem;

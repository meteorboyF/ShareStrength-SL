import axios from 'axios';

const api = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// Add interceptor to include token
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Conversation APIs
export const getConversations = () => api.get('/conversations');
export const getConversation = (id, page = 1) => api.get(`/conversations/${id}?page=${page}`);
export const getOrCreateConversation = (data) => api.post('/conversations/get-or-create', data);

// Message APIs
export const sendMessage = (data) => api.post('/messages', data);
export const markMessageAsRead = (id) => api.patch(`/messages/${id}/read`);
export const markConversationAsRead = (conversationId) => api.patch(`/conversations/${conversationId}/read`);

export default api;

import api from "@/utils/api";

const fetchNotifications = async (page = 1) => {
    try {
        const response = await api.get(`/notifications?page=${page}`);
        return response; // Return the response directly since it already has the structure we need
    } catch (error) {
        console.error('Error fetching notifications:', error);
        throw error;
    }
};

const markAsRead = async (notificationId) => {
    try {
        const response = await api.post(`/notifications/${notificationId}/mark-as-read`);
        return response.data;
    } catch (error) {
        console.error('Error marking notification as read:', error);
        throw error;
    }
};

const markAllAsRead = async () => {
    try {
        const response = await api.post('/notifications/mark-all-as-read');
        return response.data;
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
        throw error;
    }
};

export default {
    fetchNotifications,
    markAsRead,
    markAllAsRead
};
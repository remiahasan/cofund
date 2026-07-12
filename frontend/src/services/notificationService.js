import api from './api'

export const notificationService = {
    getAll: () => api.get('/notification'),
    getUnreadCount: () => api.get('/notification/unread-count'),
    markAsRead: (id) => api.patch(`/notification/${id}/read`),
    destroy: (id) => api.delete(`/notification/${id}`),
}
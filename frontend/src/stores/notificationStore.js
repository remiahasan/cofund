import { defineStore } from 'pinia'
import { ref } from 'vue'
import { notificationService } from '@/services/notificationService'

export const useNotificationStore = defineStore('notification', () => {
    const notifications = ref([])
    const unreadCount = ref(0)
    const isLoading = ref(false)

    async function fetchNotifications(params = {}) {
        isLoading.value = true
        try {
            const res = await notificationService.getAll(params)
            notifications.value = res.data.data
            return res
        } finally {
            isLoading.value = false
        }
    }

    async function fetchUnreadCount() {
        try {
            const res = await notificationService.getUnreadCount()
            unreadCount.value = res.data.data.count
        } catch (error) {
        }
    }

    async function markAsRead(notif) {
        if (notif.read_at) return
        try {
            await notificationService.markAsRead(notif.id)
            notif.read_at = new Date().toISOString()
            unreadCount.value = Math.max(0, unreadCount.value - 1)
        } catch (error) {
        }
    }

    async function markAllAsRead() {
        try {
            await notificationService.markAllAsRead()
            notifications.value.forEach(n => { n.read_at = n.read_at || new Date().toISOString() })
            unreadCount.value = 0
        } catch (error) {
        }
    }

    return { notifications, unreadCount, isLoading, fetchNotifications, fetchUnreadCount, markAsRead, markAllAsRead }
})
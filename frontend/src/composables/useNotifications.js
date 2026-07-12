import { storeToRefs } from 'pinia'
import { onMounted, onUnmounted } from 'vue'
import { useNotificationStore } from '@/stores/notificationStore'

const POLL_INTERVAL = 30000 // 30 detik

export function useNotifications({ poll = false } = {}) {
    const store = useNotificationStore()
    const { notifications, unreadCount, isLoading } = storeToRefs(store)

    let intervalId = null

    if (poll) {
        onMounted(() => {
            store.fetchUnreadCount()
            intervalId = setInterval(() => store.fetchUnreadCount(), POLL_INTERVAL)
        })
        onUnmounted(() => {
            if (intervalId) clearInterval(intervalId)
        })
    }

    return {
        notifications, unreadCount, isLoading,
        fetchNotifications: store.fetchNotifications,
        fetchUnreadCount: store.fetchUnreadCount,
        markAsRead: store.markAsRead,
        markAllAsRead: store.markAllAsRead,
    }
}
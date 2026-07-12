<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import dayjs from 'dayjs'
import { useNotifications } from '@/composables/useNotifications'

const router = useRouter()
const isOpen = ref(false)

const { notifications, unreadCount, isLoading, fetchNotifications, markAsRead } = useNotifications({ poll: true })

function toggleDropdown() {
    isOpen.value = !isOpen.value
    if (isOpen.value) fetchNotifications({ per_page: 10 })
}

async function handleClickNotification(notif) {
    await markAsRead(notif)
    isOpen.value = false

    if (notif.data?.campaign_id) {
        router.push({ name: 'campaign.detail', params: { id: notif.data.campaign_id } })
    }
}

function closeOnOutsideClick(e) {
    if (!e.target.closest('.notification-bell-wrapper')) isOpen.value = false
}

onMounted(() => {
    document.addEventListener('click', closeOnOutsideClick)
})
</script>

<template>
    <div class="notification-bell-wrapper relative">
        <button @click.stop="toggleDropdown" class="relative p-2 rounded-full hover:bg-gray-100">
            <i class="pi pi-bell text-xl"></i>
            <span v-if="unreadCount > 0"
                class="absolute -top-0.5 -right-0.5 bg-red-600 text-white text-[10px] rounded-full min-w-[16px] h-4 flex items-center justify-center px-1">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <div v-if="isOpen" class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-96 overflow-y-auto">
            <div class="p-3 border-b border-gray-200 font-semibold text-sm">Notifikasi</div>

            <div v-if="isLoading" class="p-6 text-center text-gray-400 text-sm">Memuat...</div>
            <div v-else-if="notifications.length === 0" class="p-6 text-center text-gray-400 text-sm">Belum ada notifikasi.</div>

            <div v-else>
                <div v-for="notif in notifications" :key="notif.id"
                    @click="handleClickNotification(notif)"
                    class="p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200"
                    :class="!notif.read_at ? 'bg-blue-50' : ''">
                    <p class="text-sm font-medium">{{ notif.title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ notif.body }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ dayjs(notif.created_at).fromNow ? dayjs(notif.created_at).format('DD MMM, HH:mm') : notif.created_at }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
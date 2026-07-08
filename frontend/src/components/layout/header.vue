<script setup>
import { ref } from 'vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

const props = defineProps({
    sidebarOpen: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['sidebar-toggle']);

const toggleSidebar = () => {
    emit('sidebar-toggle');
};

const searchQuery = ref('');
const notifVisible = ref(false);
const unreadCount = ref(3);
const userMenuVisible = ref(false);

</script>

<template>
    <header
        class="h-[70px] bg-white/85 backdrop-blur-md backdrop-saturate-150 border-b border-gray-200 flex items-center justify-between px-5 lg:px-8 sticky top-0 z-[999] gap-4">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs hidden md:inline">
                    Enterprise Console
                </span>
                <i class="pi pi-chevron-right hidden md:inline text-blue-600" style="font-size: 0.6rem;"></i>
                <span class="font-semibold text-sm text-gray-900">
                    Dashboard
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Global Search -->
            <div class="hidden md:flex relative border border-blue-500 bg-white rounded-lg gap-2 items-center">
                <span class="p-input-icon-left">
                    <i class="pi pi-search mx-2" style="font-size: 0.8rem;" />
                    <InputText v-model="searchQuery" placeholder="Search records, reports…"
                        class="w-56 focus:w-80 pr-10 text-[13px] h-[34px] transition-all duration-300 p-inputtext-sm" />
                </span>
            </div>

            <!-- Notification Bell -->
            <div class="relative">
                <Button icon="pi pi-bell" text rounded severity="secondary" size="small"
                    @click="notifVisible = !notifVisible" />
                <span v-if="unreadCount > 0"
                    class="absolute top-0 left-1 w-4 h-4 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white pointer-events-none">{{
                    unreadCount }}</span>
            </div>

            <!-- User Avatar -->
            <div class="w-8 h-8 rounded-lg bg-linear-to-br from-blue-600 to-indigo-600 text-white text-[11px] font-bold flex items-center justify-center cursor-pointer transition-opacity duration-200 hover:opacity-85 shrink-0"
                @click="userMenuVisible = !userMenuVisible" v-tooltip.bottom="'Abdil Mascitra'">
                AM
            </div>
        </div>
    </header>
    <Transition name="slide-down">
        <div v-if="notifVisible" class="notif-panel enterprise-card">
            <div class="flex justify-content-between align-items-center mb-3">
                <h3 class="m-0 font-bold text-sm text-900">Notifications</h3>
                <div class="flex gap-2 align-items-center">
                    <span class="status-badge badge-info">{{ unreadCount }} new</span>
                    <Button label="Clear all" variant="text" size="small" class="text-xs p-0"
                        @click="clearNotifications" />
                </div>
            </div>
            <div class="flex flex-column gap-2">
                <div v-for="n in notifications" :key="n.id" class="notif-item" :class="{ 'notif-unread': !n.read }"
                    @click="n.read = true">
                    <div class="icon-badge flex-shrink-0"
                        style="width:32px;height:32px;border-radius:8px;font-size:0.8rem;"
                        :style="{ background: n.iconBg, color: n.iconColor }">
                        <i :class="n.icon"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="m-0 text-sm font-semibold text-900 truncate">{{ n.title }}</p>
                        <p class="m-0 text-xs text-muted mt-1 truncate">{{ n.message }}</p>
                        <p class="m-0 text-xs text-subtle mt-1">{{ n.time }}</p>
                    </div>
                </div>
            </div>
            <Button label="View all notifications" variant="text" icon="pi pi-arrow-right" iconPos="right" size="small"
                class="w-full mt-3 border-top-1 surface-border pt-3" />
        </div>
    </Transition>
</template>

<style>
.notif-panel {
    position: fixed;
    top: calc(var(--header-height) + 8px);
    right: 1.5rem;
    width: 340px;
    max-height: 480px;
    overflow-y: auto;
    padding: 1.25rem;
    z-index: 1100;
    box-shadow: var(--shadow-xl) !important;
}

.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: background var(--t-fast);
}

.notif-item:hover {
    background: var(--bg-color);
}

.notif-unread {
    background: var(--primary-light);
}

.notif-unread:hover {
    background: #e0e9fd;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

</style>
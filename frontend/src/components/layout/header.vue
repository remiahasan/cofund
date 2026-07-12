<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import { useAuthStore } from '@/stores/authStore';
import NotificationBell from '@/components/notification/NotificationBell.vue';
import { backingService } from '@/services/backingService';

const props = defineProps({
    sidebarOpen: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['sidebar-toggle']);
const authStore = useAuthStore();
const router = useRouter();

const toggleSidebar = () => {
    emit('sidebar-toggle');
};

const userMenuVisible = ref(false);

const roleLabel = { backer: 'Backer', creator: 'Creator', admin: 'Admin' };

const initials = () => {
    const name = authStore.user?.name || '';
    return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || '?';
};

async function requestCreator() {
    try {
        await backingService.requestCreator();
        await authStore.fetchProfile();
    } catch (error) {
        console.log(error);
    }
}

async function handleLogout() {
    await authStore.logout();
    router.push({ name: 'login' });
}
</script>

<template>
    <header
        class="h-[70px] bg-white/85 backdrop-blur-md backdrop-saturate-150 border-b border-gray-200 flex items-center justify-between px-5 lg:px-8 sticky top-0 z-[999] gap-4">
        <div class="flex items-center gap-2">
            <span class="font-semibold text-sm text-gray-900">
                {{ roleLabel[authStore.user?.role] || 'Dashboard' }}
            </span>
        </div>

        <div class="flex items-center gap-3 relative">
            <NotificationBell />

            <div class="relative">
                <div class="w-8 h-8 rounded-lg bg-linear-to-br from-blue-600 to-indigo-600 text-white text-[11px] font-bold flex items-center justify-center cursor-pointer transition-opacity duration-200 hover:opacity-85 shrink-0"
                    @click.stop="userMenuVisible = !userMenuVisible" v-tooltip.bottom="authStore.user?.name">
                    {{ initials() }}
                </div>

                <div v-if="userMenuVisible"
                    class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-1">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-semibold truncate">
                            {{ authStore.user?.name }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ authStore.user?.email }}
                        </p>
                    </div>

                    <!-- Tombol Pengajuan Creator -->
                    <div v-if="authStore.user?.role === 'backer'" class="px-3 py-2">
                        <Button
                            v-if="!authStore.user?.creator_request_status || authStore.user?.creator_request_status === 'none' || authStore.user?.creator_request_status === 'rejected'"
                            label="Ajukan Menjadi Creator" icon="pi pi-send" severity="info" size="small" class="w-full"
                            @click="requestCreator" />

                        <div v-else-if="authStore.user?.creator_request_status === 'pending'"
                            class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2">
                            Pengajuan creator sedang diproses.
                        </div>

                        <div v-else-if="authStore.user?.creator_request_status === 'approved'"
                            class="text-xs text-green-600 bg-green-50 rounded-lg px-3 py-2">
                            Anda telah menjadi Creator.
                        </div>
                    </div>

                    <button @click="handleLogout"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>
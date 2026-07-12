<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { LayoutDashboard, Megaphone, Wallet, Bell, ClipboardCheck, Users, BarChart3, PlusCircle } from '@lucide/vue';
import Button from 'primevue/button';
import { useAuthStore } from '@/stores/authStore';
import { dashboardRouteByRole } from '@/router';

const props = defineProps({
    sidebarOpen: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['sidebar-toggle']);
const authStore = useAuthStore();
const router = useRouter();

const toggleMobileMenu = () => {
    emit('sidebar-toggle');
};

const navItemsByRole = {
    backer: [
        { to: { name: dashboardRouteByRole.backer }, label: 'Dashboard', icon: LayoutDashboard },
        { to: { name: 'campaign.list' }, label: 'Kampanye', icon: Megaphone },
        { to: { name: 'wallet' }, label: 'Saldo', icon: Wallet },
    ],
    creator: [
        { to: { name: dashboardRouteByRole.creator }, label: 'Dashboard', icon: LayoutDashboard },
        { to: { name: 'creator.campaign.create' }, label: 'Buat Kampanye', icon: PlusCircle },
        { to: { name: 'campaign.list' }, label: 'Kampanye', icon: Megaphone },
        { to: { name: 'wallet' }, label: 'Saldo', icon: Wallet },
    ],
    admin: [
        { to: { name: dashboardRouteByRole.admin }, label: 'Dashboard', icon: LayoutDashboard },
        { to: { name: 'admin.approval' }, label: 'Approval Queue', icon: ClipboardCheck },
        { to: { name: 'admin.campaigns' }, label: 'Manajemen Kampanye', icon: Megaphone },
        { to: { name: 'admin.users' }, label: 'Manajemen User', icon: Users },
        { to: { name: 'admin.overview' }, label: 'Overview Platform', icon: BarChart3 },
    ],
};

const navItems = computed(() => navItemsByRole[authStore.user?.role] || []);

async function handleLogout() {
    await authStore.logout();
    router.push({ name: 'login' });
}
</script>

<template>
    <aside class="layout-sidebar" :class="{ 'layout-sidebar-collapsed': !sidebarOpen }">
        <!-- Brand Header -->
        <div class="sidebar-header">
            <Transition name="label-fade">
                <div v-if="sidebarOpen" class="sidebar-brand">
                    <div class="brand-icon">
                        <div class="pi pi-building">
                            <svg width="34" height="43" viewBox="0 0 34 43" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.90036 3.85577C10.3863 -0.084084 17.084 -1.02412 22.5998 1.12552C30.5003 3.95945 35.0691 13.3391 32.878 21.3501C31.9172 24.6817 30.051 27.64 28.2608 30.5777C26.6295 27.7714 24.3278 25.442 21.487 23.8592C23.7887 21.4469 25.3439 17.8457 23.761 14.6247C21.8671 9.53745 14.4021 8.04445 10.8701 12.2746C7.55233 15.5371 8.51311 20.8732 11.5751 23.949C8.7412 25.3245 6.57774 27.6677 5.25063 30.5085C2.09875 26.5894 -0.424145 21.7648 0.0596964 16.5808C-0.202961 11.6871 2.30611 7.04221 5.90036 3.85577Z"
                                    fill="#515DEF" />
                                <path
                                    d="M11.4097 38.2014C12.4396 33.4736 13.0962 28.6767 14.057 23.935C15.6882 23.9419 17.3125 23.9419 18.9438 23.9419C19.946 28.649 20.6649 33.4114 21.6187 38.1323C19.9598 39.6461 18.6396 41.927 16.5177 42.6943C14.4302 41.8856 13.0962 39.6599 11.4097 38.2014Z"
                                    fill="#515DEF" />
                            </svg>
                        </div>
                    </div>
                    <span class="brand-name">Cofund</span>
                </div>
            </Transition>
            <Button :icon="sidebarOpen ? 'pi pi-times' : 'pi pi-bars'" @click="toggleMobileMenu" text rounded
                severity="secondary" size="small" />
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section-label" v-if="sidebarOpen">Main</div>

            <router-link v-for="item in navItems" :key="item.label" :to="item.to" class="nav-item" active-class="active"
                @click="onMenuItemClick" v-tooltip.right="!sidebarOpen ? item.label : null">
                <component :is="item.icon" :size="18" class="nav-icon flex-shrink-0" />
                <Transition name="label-fade">
                    <span v-if="sidebarOpen" class="nav-label">{{ item.label }}</span>
                </Transition>
            </router-link>

            <div class="nav-section-label mt-3" v-if="sidebarOpen">Akun</div>

            <button class="nav-item w-full text-left" @click="handleLogout" v-tooltip.right="!sidebarOpen ? 'Logout' : null">
                <i class="pi pi-sign-out nav-icon flex-shrink-0"></i>
                <Transition name="label-fade">
                    <span v-if="sidebarOpen" class="nav-label">Logout</span>
                </Transition>
            </button>
        </nav>
    </aside>
</template>

<style>
:root {
    --sidebar-width: 260px;
    --sidebar-collapsed-width: 80px;
    --card-bg: #f1f5ff;
    --border-color: #e5e7eb;
    --t-base: 0.3s ease;
    --header-height: 70px;
    --radius-md: 8px;
    --text-main: #111827;
}

.layout-sidebar {
    width: var(--sidebar-width);
    background: var(--card-bg);
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    transition: width var(--t-base);
    z-index: 45;
    overflow: hidden;
}

.sidebar-header {
    height: var(--header-height);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 2rem;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    overflow: hidden;
}

.brand-icon {
    width: 34px;
    height: 43px;
    border-radius: var(--radius-md);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(26, 86, 219, 0.3);
}

.brand-name {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.03em;
    white-space: nowrap;
}

.sidebar-nav {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
}

.nav-section-label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: var(--text-subtle);
    padding: 0.5rem 0.75rem 0.35rem;
    white-space: nowrap;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0.75rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: var(--radius-md);
    transition: all var(--t-fast);
    white-space: nowrap;
    margin-bottom: 2px;
    position: relative;
    font-size: 0.875rem;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
}

.nav-item:hover {
    background: var(--bg-color);
    color: var(--text-main);
}

.nav-item.active {
    background: var(--primary-light);
    color: var(--primary-color);
    font-weight: 600;
}

.nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 25%;
    bottom: 25%;
    width: 3px;
    border-radius: 0 3px 3px 0;
    background: var(--primary-color);
    margin-left: -0.75rem;
}

.nav-icon {
    flex-shrink: 0;
}

.nav-label {
    flex: 1;
    overflow: hidden;
}

.layout-sidebar.layout-sidebar-collapsed {
    width: var(--sidebar-collapsed-width);
}
</style>
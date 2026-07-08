<script setup>
import Sidebar from '@/components/layout/sidebar.vue';
import Header from '@/components/layout/header.vue';

import { ref } from 'vue';

const sidebarOpen = ref(true);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
}
</script>

<template>
    <div class="min-h-screen flex">
        <Sidebar :sidebar-open="sidebarOpen" @sidebar-toggle="toggleSidebar" />
        <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'ml-[260px]' : 'ml-[80px]'">
            <Header />
            <main>
                <router-view />
            </main>
        </div>
    </div>
</template>
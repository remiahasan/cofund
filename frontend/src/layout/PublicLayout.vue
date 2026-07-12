<script setup>
import Sidebar from '@/components/layout/Sidebar.vue'
import Header from '@/components/layout/Header.vue'
import { onMounted, onBeforeUnmount, ref } from 'vue'

const sidebarOpen = ref(true)

const handleResize = () => {
    sidebarOpen.value = window.innerWidth >= 768
}

onMounted(() => {
    handleResize()
    window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize)
})

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}
</script>

<template>
    <div class="min-h-screen flex bg-gray-50">
        <Sidebar
            :sidebar-open="sidebarOpen"
            @sidebar-toggle="toggleSidebar"
        />

        <div
            class="flex flex-col transition-all duration-300"
            :class="sidebarOpen
                ? 'ml-[260px] w-[calc(100%-260px)]'
                : 'ml-[80px] w-[calc(100%-80px)]'"
        >
            <Header />

            <main class="flex-1 overflow-auto p-6">
                <router-view />
            </main>
        </div>
    </div>
</template>
<script setup>
import { computed } from 'vue'
import Logo from '@/icon/Group 47602.svg'
import { useAuthStore } from '@/stores/authStore'
import { dashboardRouteByRole } from '@/router'

const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <header class="flex items-center justify-between px-8 py-4 border-b">
            <router-link to="/" class="flex items-center gap-2">
                <img :src="Logo" alt="Cofund" class="h-8" />
                <span class="font-bold text-xl">Cofund</span>
            </router-link>

            <nav class="flex items-center gap-6">
                <router-link to="/" class="text-gray-600 hover:text-blue-700">Kampanye</router-link>

                <router-link v-if="isAuthenticated"
                    :to="{ name: dashboardRouteByRole[authStore.user?.role] || 'dashboard.backer' }"
                    class="bg-blue-700 text-white px-4 py-2 rounded-sm font-semibold">
                    Dashboard
                </router-link>
                <template v-else>
                    <router-link to="/login" class="text-gray-600 hover:text-blue-700">Masuk</router-link>
                    <router-link to="/register" class="bg-blue-700 text-white px-4 py-2 rounded-sm font-semibold">Daftar</router-link>
                </template>
            </nav>
        </header>

        <main class="flex-1">
            <router-view />
        </main>
    </div>
</template>
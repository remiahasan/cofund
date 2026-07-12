<script setup>
import { ref, computed } from 'vue'
import Logo from '@/icon/Group 47602.svg'
import { useAuthStore } from '@/stores/authStore'
import { dashboardRouteByRole } from '@/router'

const mobileMenuOpen = ref(false)

const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <header class="fixed top-0 left-0 right-0 bg-gray-100 z-50 shadow-sm">
            <div class="flex items-center justify-between px-5 md:px-8 py-4">

                <!-- Logo -->
                <router-link to="/" class="flex items-center gap-2">
                    <img :src="Logo" alt="Cofund" class="h-8" />
                    <span class="font-bold text-xl">Cofund</span>
                </router-link>

                <!-- Menu Desktop -->
                <nav class="hidden md:flex items-center gap-6">
                    <router-link to="/" class="text-gray-600 hover:text-blue-700">
                        Kampanye
                    </router-link>

                    <router-link v-if="isAuthenticated"
                        :to="{ name: dashboardRouteByRole[authStore.user?.role] || 'dashboard.backer' }"
                        class="bg-blue-700 text-white px-4 py-2 rounded-sm font-semibold">
                        Dashboard
                    </router-link>

                    <template v-else>
                        <router-link to="/login" class="text-gray-600 hover:text-blue-700">
                            Masuk
                        </router-link>

                        <router-link to="/register" class="bg-blue-700 text-white px-4 py-2 rounded-sm font-semibold">
                            Daftar
                        </router-link>
                    </template>
                </nav>

                <!-- Hamburger -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden">
                    <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Menu Mobile -->
            <nav v-if="mobileMenuOpen" class="md:hidden border-t bg-gray-100 px-5 py-4 flex flex-col gap-4">
                <router-link @click="mobileMenuOpen = false" to="/" class="text-gray-700">
                    Kampanye
                </router-link>

                <router-link v-if="isAuthenticated" @click="mobileMenuOpen = false"
                    :to="{ name: dashboardRouteByRole[authStore.user?.role] || 'dashboard.backer' }"
                    class="bg-blue-700 text-white text-center py-2 rounded-sm">
                    Dashboard
                </router-link>

                <template v-else>
                    <router-link @click="mobileMenuOpen = false" to="/login" class="text-gray-700">
                        Masuk
                    </router-link>

                    <router-link @click="mobileMenuOpen = false" to="/register"
                        class="bg-blue-700 text-white text-center py-2 rounded-sm">
                        Daftar
                    </router-link>
                </template>
            </nav>
        </header>

        <main class="flex-1 mt-10 md:mt-15">
            <router-view />
        </main>
    </div>
</template>
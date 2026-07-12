<script setup>
import { onMounted, ref } from 'vue'
import { adminService } from '@/services/adminService'

const pendingCount = ref(0)

onMounted(async () => {
    try {
        const res = await adminService.getPendingCampaigns()
        pendingCount.value = res.data.data.length
    } catch (error) {
        pendingCount.value = 0
    }
})
</script>

<template>
    <div class="p-4 sm:p-8">
        <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <router-link :to="{ name: 'admin.approval' }" class="border rounded-xl p-5 hover:shadow-md transition">
                <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold mt-1">{{ pendingCount }} kampanye</p>
                <p class="text-blue-700 text-sm font-semibold mt-2">Buka Approval Queue →</p>
            </router-link>

            <router-link :to="{ name: 'admin.campaigns' }" class="border rounded-xl p-5 hover:shadow-md transition">
                <p class="text-sm text-gray-500">Kelola</p>
                <p class="text-2xl font-bold mt-1">Semua Kampanye</p>
                <p class="text-blue-700 text-sm font-semibold mt-2">Buka Manajemen Kampanye →</p>
            </router-link>

            <router-link :to="{ name: 'admin.users' }" class="border rounded-xl p-5 hover:shadow-md transition">
                <p class="text-sm text-gray-500">Kelola</p>
                <p class="text-2xl font-bold mt-1">Daftar User</p>
                <p class="text-blue-700 text-sm font-semibold mt-2">Buka Manajemen User →</p>
            </router-link>

            <router-link :to="{ name: 'admin.overview' }" class="border rounded-xl p-5 hover:shadow-md transition">
                <p class="text-sm text-gray-500">Statistik</p>
                <p class="text-2xl font-bold mt-1">Overview Platform</p>
                <p class="text-blue-700 text-sm font-semibold mt-2">Lihat Overview →</p>
            </router-link>
        </div>
    </div>
</template>
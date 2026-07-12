<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useDashboardStore } from '@/stores/dashboardStore'
import { useAuthStore } from '@/stores/authStore'

const dashboardStore = useDashboardStore()
const authStore = useAuthStore()
const { backerSummary, isLoading } = storeToRefs(dashboardStore)

onMounted(() => {
    dashboardStore.fetchBackerDashboard()
    authStore.fetchProfile()
})

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}
</script>

<template>
    <div class="p-4 sm:p-8">
        <h1 class="text-2xl font-bold mb-6">Dashboard Backer</h1>

        <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat ringkasan...</div>

        <div v-else-if="backerSummary" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Saldo Saat Ini</p>
                <p class="text-xl font-bold mt-1">{{ formatCurrency(authStore.user?.balance) }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Total Dana Dibacking</p>
                <p class="text-xl font-bold mt-1">{{ formatCurrency(backerSummary.total_backing) }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Kampanye Diikuti</p>
                <p class="text-xl font-bold mt-1">{{ backerSummary.campaign_joined }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Total Refund Diterima</p>
                <p class="text-xl font-bold mt-1">{{ formatCurrency(backerSummary.total_refund) }}</p>
            </div>
        </div>

        <p class="text-sm text-gray-400">
            Riwayat backing per-kampanye belum tersedia dari backend (belum ada endpoint <code>GET /backings/mine</code>) — saat ini dashboard backer hanya menampilkan ringkasan angka.
        </p>
    </div>
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { adminService } from '@/services/adminService'
import ErrorState from '@/components/common/ErrorState.vue'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const overview = ref(null)
const chartRaw = ref([])
const isLoading = ref(false)
const hasError = ref(false)

const statusLabel = { draft: 'Draft', review: 'Review', active: 'Aktif', success: 'Berhasil', failed: 'Gagal' }

async function load() {
    isLoading.value = true
    hasError.value = false
    try {
        const [overviewRes, chartRes] = await Promise.all([
            adminService.getOverview(),
            adminService.getOverviewChart(),
        ])
        overview.value = overviewRes.data.data
        chartRaw.value = chartRes.data.data
    } catch (error) {
        hasError.value = true
    } finally {
        isLoading.value = false
    }
}

onMounted(load)

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}

const totalCampaigns = computed(() => {
    if (!overview.value) return 0
    return Object.values(overview.value.campaigns || {}).reduce((a, b) => a + b, 0)
})

const chartData = computed(() => ({
    labels: chartRaw.value.map(m => m.month),
    datasets: [{
        label: 'Kampanye Baru',
        data: chartRaw.value.map(m => m.total_campaign),
        backgroundColor: '#1d4ed8',
        borderRadius: 4,
    }]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
}
</script>

<template>
    <div class="p-4 sm:p-8">
        <h1 class="text-2xl font-bold mb-6">Overview Platform</h1>

        <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat data...</div>
        <ErrorState v-else-if="hasError" message="Gagal memuat overview platform." @retry="load" />

        <template v-else-if="overview">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Total Kampanye</p>
                    <p class="text-xl font-bold mt-1">{{ totalCampaigns }}</p>
                </div>
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Total Dana Terkumpul</p>
                    <p class="text-xl font-bold mt-1">{{ formatCurrency(overview.total_collected) }}</p>
                </div>
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Total Platform Fee</p>
                    <p class="text-xl font-bold mt-1">{{ formatCurrency(overview.platform_fee) }}</p>
                </div>
                <div class="border rounded-xl p-4">
                    <p class="text-xs text-gray-500">Kampanye Aktif</p>
                    <p class="text-xl font-bold mt-1">{{ overview.campaigns?.active || 0 }}</p>
                </div>
            </div>

            <div class="border rounded-xl p-4 mb-8">
                <h2 class="font-semibold mb-3">Kampanye per Status</h2>
                <div class="flex flex-wrap gap-3">
                    <div v-for="(count, status) in overview.campaigns" :key="status" class="border rounded-lg px-4 py-2 text-sm">
                        <span class="text-gray-500">{{ statusLabel[status] || status }}:</span>
                        <span class="font-bold ml-1">{{ count }}</span>
                    </div>
                </div>
            </div>

            <div class="border rounded-xl p-4">
                <h2 class="font-semibold mb-3">Kampanye Baru per Bulan</h2>
                <div class="h-64">
                    <Bar :data="chartData" :options="chartOptions" />
                </div>
            </div>
        </template>
    </div>
</template>
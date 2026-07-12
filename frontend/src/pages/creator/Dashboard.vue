<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { storeToRefs } from 'pinia'
import dayjs from 'dayjs'
import { useDashboardStore } from '@/stores/dashboardStore'
import { useCampaignStore } from '@/stores/campaignStore'
import FundingChart from '@/components/creator/FundingChart.vue'

const router = useRouter()
const toast = useToast()
const dashboardStore = useDashboardStore()
const campaignStore = useCampaignStore()
const { creatorSummary, creatorCampaigns, fundingChart, isLoading } = storeToRefs(dashboardStore)

onMounted(() => {
    dashboardStore.fetchCreatorDashboard()
    dashboardStore.fetchFundingChart()
})

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}

const statusLabel = { draft: 'Draft', review: 'Menunggu Review', active: 'Aktif', success: 'Berhasil', failed: 'Gagal' }
const statusColor = {
    draft: 'bg-gray-200 text-gray-700', review: 'bg-yellow-100 text-yellow-700',
    active: 'bg-blue-100 text-blue-700', success: 'bg-green-100 text-green-700', failed: 'bg-red-100 text-red-700',
}

function goCreate() { router.push({ name: 'creator.campaign.create' }) }
function goEdit(id) { router.push({ name: 'campaign.edit', params: { id } }) }

async function submitForReview(id) {
    try {
        await campaignStore.submitCampaign(id)
        toast.success('Kampanye diajukan untuk review')
        dashboardStore.fetchCreatorDashboard()
    } catch (error) {
        toast.error(error.response?.data?.message || 'Gagal mengajukan review')
    }
}
</script>

<template>
    <div class="p-4">
        <div class="md:flex md:justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Dashboard Creator</h1>
            <button @click="goCreate" class="bg-blue-700 mt-3 md:mt-0 text-white px-4 py-2 rounded-sm font-semibold">+ Buat Kampanye</button>
        </div>

        <div v-if="creatorSummary" class="grid md:grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Total Kampanye</p>
                <p class="text-xl font-bold mt-1">{{ creatorSummary.total_campaign }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Total Backer</p>
                <p class="text-xl font-bold mt-1">{{ creatorSummary.total_backer }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Total Terkumpul</p>
                <p class="text-xl font-bold mt-1">{{ formatCurrency(creatorSummary.total_collected) }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Kampanye Aktif</p>
                <p class="text-xl font-bold mt-1">{{ creatorSummary.active_campaign }}</p>
            </div>
        </div>

        <div class="border rounded-xl p-4 mb-8">
            <h2 class="font-semibold mb-3">Grafik Funding Harian (semua kampanye)</h2>
            <FundingChart :stats="fundingChart" />
        </div>

        <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat kampanye...</div>
        <div v-else-if="creatorCampaigns.length === 0" class="text-center py-20 text-gray-500">
            Anda belum punya kampanye. Mulai buat kampanye pertama Anda!
        </div>

        <div v-else class="flex flex-col gap-4">
            <div v-for="c in creatorCampaigns" :key="c.id" class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-semibold">{{ c.title }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="statusColor[c.status]">{{ statusLabel[c.status] }}</span>
                    </div>
                    <p class="text-sm text-gray-500">Deadline: {{ dayjs(c.deadline).format('DD MMM YYYY') }} · {{ c.total_backer }} backer</p>
                    <div class="w-full bg-gray-100 rounded-full h-2 mt-2">
                        <div class="bg-blue-700 h-2 rounded-full" :style="{ width: c.percentage + '%' }"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ c.percentage }}% dari target</p>
                </div>

                <div class="flex gap-2">
                    <button v-if="c.status === 'draft'" @click="goEdit(c.id)" class="border px-4 py-2 rounded-sm text-sm font-semibold">Edit</button>
                    <button v-if="c.status === 'draft'" @click="submitForReview(c.id)" class="bg-blue-700 text-white px-4 py-2 rounded-sm text-sm font-semibold">Ajukan Review</button>
                    <router-link :to="{ name: 'campaign.detail', params: { id: c.id } }" class="border px-4 py-2 rounded-sm text-sm font-semibold">Lihat</router-link>
                </div>
            </div>
        </div>
    </div>
</template>
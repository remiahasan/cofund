<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { storeToRefs } from 'pinia'
import dayjs from 'dayjs'
import { useCampaignStore } from '@/stores/campaignStore'

const router = useRouter()
const toast = useToast()
const store = useCampaignStore()
const { myCampaigns, isLoading } = storeToRefs(store)

onMounted(() => {
    store.fetchMyCampaigns()
})

const statusLabel = {
    draft: 'Draft',
    review: 'Menunggu Review',
    active: 'Aktif',
    success: 'Berhasil',
    failed: 'Gagal',
}

const statusColor = {
    draft: 'bg-gray-200 text-gray-700',
    review: 'bg-yellow-100 text-yellow-700',
    active: 'bg-blue-100 text-blue-700',
    success: 'bg-green-100 text-green-700',
    failed: 'bg-red-100 text-red-700',
}

function percentage(c) {
    const target = Number(c.target_amount) || 0
    const collected = Number(c.collected_amount) || 0
    if (target <= 0) return 0
    return Math.min(100, Math.round((collected / target) * 100))
}

function goCreate() {
    router.push({ name: 'campaign.create' })
}

function goEdit(id) {
    router.push({ name: 'campaign.edit', params: { id } })
}

async function submitForReview(id) {
    try {
        await store.submitCampaign(id)
        toast.success('Kampanye diajukan untuk review')
        store.fetchMyCampaigns()
    } catch (error) {
        toast.error(error.response?.data?.message || 'Gagal mengajukan review')
    }
}
</script>

<template>
    <div class="p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Dashboard Creator</h1>
            <button @click="goCreate" class="bg-blue-700 text-white px-4 py-2 rounded-sm font-semibold">+ Buat Kampanye</button>
        </div>

        <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat kampanye...</div>
        <div v-else-if="myCampaigns.length === 0" class="text-center py-20 text-gray-500">
            Anda belum punya kampanye. Mulai buat kampanye pertama Anda!
        </div>

        <div v-else class="flex flex-col gap-4">
            <div v-for="c in myCampaigns" :key="c.id" class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-semibold">{{ c.title }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="statusColor[c.status]">{{ statusLabel[c.status] }}</span>
                    </div>
                    <p class="text-sm text-gray-500">Deadline: {{ dayjs(c.deadline).format('DD MMM YYYY') }}</p>
                    <div class="w-full bg-gray-100 rounded-full h-2 mt-2">
                        <div class="bg-blue-700 h-2 rounded-full" :style="{ width: percentage(c) + '%' }"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ percentage(c) }}% dari target</p>
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
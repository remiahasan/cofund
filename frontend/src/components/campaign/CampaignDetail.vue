<script setup>
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import dayjs from 'dayjs'
import { useCampaign } from '@/composables/useCampaign'
import ProgressBar from 'primevue/progressbar'
import noImage from '@/images/no-image.jpg'

const route = useRoute()
const { currentCampaign, isLoading, fetchOne } = useCampaign()

onMounted(() => {
    fetchOne(route.params.id)
})

const percentage = computed(() => {
    if (!currentCampaign.value) return 0
    const target = Number(currentCampaign.value.target_amount) || 0
    const collected = Number(currentCampaign.value.collected_amount) || 0
    if (target <= 0) return 0
    return Math.min(100, Math.round((collected / target) * 100))
})

const daysLeft = computed(() => {
    if (!currentCampaign.value) return 0
    const diff = dayjs(currentCampaign.value.deadline).diff(dayjs(), 'day')
    return diff > 0 ? diff : 0
})

const primaryImage = computed(() => {
    const img = currentCampaign.value?.images?.find(i => i.is_primary) || currentCampaign.value?.images?.[0]
    return img?.url || noImage
})

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}
</script>

<template>
    <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat kampanye...</div>

    <div v-else-if="currentCampaign" class="max-w-5xl mx-auto px-6 py-10">
        <img :src="primaryImage" :alt="currentCampaign.title" class="w-full aspect-video object-cover rounded-2xl" />

        <h1 class="text-3xl font-bold mt-6">{{ currentCampaign.title }}</h1>
        <p class="text-gray-500 mt-1">oleh {{ currentCampaign.user?.name || '-' }}</p>

        <div class="mt-6">
            <ProgressBar :value="percentage" :showValue="false" class="!h-3" />
            <div class="flex justify-between mt-2 text-sm">
                <span class="font-bold text-blue-700">{{ percentage }}%</span>
                <span class="text-gray-500">{{ daysLeft }} hari lagi</span>
            </div>
            <div class="text-gray-700 mt-2">
                Terkumpul <span class="font-semibold">{{ formatCurrency(currentCampaign.collected_amount) }}</span>
                dari target {{ formatCurrency(currentCampaign.target_amount) }}
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">Deskripsi</h2>
            <p class="text-gray-700 whitespace-pre-line">{{ currentCampaign.description }}</p>
        </div>

        <!-- TODO: sesuaikan struktur field tiers/updates setelah response backend dikonfirmasi -->
        <div class="mt-8" v-if="currentCampaign.tiers?.length">
            <h2 class="text-xl font-semibold mb-4">Pilihan Tier</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="tier in currentCampaign.tiers" :key="tier.id" class="border rounded-xl p-4">
                    <h3 class="font-semibold">{{ tier.name }}</h3>
                    <p class="text-blue-700 font-bold mt-1">{{ formatCurrency(tier.min_amount) }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ tier.reward_description }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        {{ tier.remaining_quota > 0 ? `Sisa ${tier.remaining_quota} slot` : (tier.quota === 0 ? 'Tidak terbatas' : 'Kuota habis') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8" v-if="currentCampaign.updates?.length">
            <h2 class="text-xl font-semibold mb-4">Update Kampanye</h2>
            <div v-for="update in currentCampaign.updates" :key="update.id" class="border-b py-4">
                <h3 class="font-semibold">{{ update.title }}</h3>
                <p class="text-xs text-gray-400">{{ dayjs(update.created_at).format('DD MMM YYYY') }}</p>
                <p class="text-gray-700 mt-2 whitespace-pre-line">{{ update.content }}</p>
            </div>
        </div>
    </div>

    <div v-else class="text-center py-20 text-gray-500">Kampanye tidak ditemukan.</div>
</template>
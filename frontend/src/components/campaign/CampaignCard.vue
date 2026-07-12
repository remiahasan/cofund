<script setup>
import { computed } from 'vue'
import dayjs from 'dayjs'
import noImage from '@/images/no-image.jpg'
import ProgressBar from 'primevue/progressbar'

const props = defineProps({
    campaign: { type: Object, required: true }
})

const primaryImage = computed(() => {
    const img = props.campaign.images?.find(i => i.is_primary) || props.campaign.images?.[0]
    const urlImage = 'http://localhost:8000' + img?.url
    return urlImage
})

const percentage = computed(() => {
    const target = Number(props.campaign.target_amount) || 0
    const collected = Number(props.campaign.collected_amount) || 0
    if (target <= 0) return 0
    return Math.min(100, Math.round((collected / target) * 100))
})

const daysLeft = computed(() => {
    const diff = dayjs(props.campaign.deadline).diff(dayjs(), 'day')
    return diff > 0 ? diff : 0
})

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}
</script>

<template>
    <router-link :to="{ name: 'campaign.detail', params: { id: campaign.id } }"
        class="bg-white rounded-2xl overflow-hidden shadow-[0_4px_8px_rgba(0,0,0,0.09)] transition-transform duration-200 hover:-translate-y-1.5 hover:shadow-[0_12px_24px_rgba(0,0,0,0.13)] flex flex-col">
        <img :src="primaryImage" :alt="campaign.title" class="w-full aspect-video object-cover" />
        <div class="p-4 flex flex-col gap-2 flex-1">
            <span v-if="campaign.category" class="text-xs text-blue-700 font-semibold uppercase">{{ campaign.category.name }}</span>
            <h3 class="font-semibold text-gray-800 line-clamp-2">{{ campaign.title }}</h3>
            <ProgressBar :value="percentage" :showValue="false" class="!h-2" />
            <div class="flex justify-between text-sm">
                <span class="font-bold text-blue-700">{{ percentage }}%</span>
                <span class="text-gray-500">{{ daysLeft }} hari lagi</span>
            </div>
            <div class="text-sm text-gray-600">
                Terkumpul <span class="font-semibold">{{ formatCurrency(campaign.collected_amount) }}</span>
                dari {{ formatCurrency(campaign.target_amount) }}
            </div>
        </div>
    </router-link>
</template>
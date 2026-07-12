<script setup>
import { onMounted, reactive, watch } from 'vue'
import { useCampaign } from '@/composables/useCampaign'
import CampaignCard from '@/components/campaign/CampaignCard.vue'
import SkeletonCard from '@/components/common/SkeletonCard.vue'
import EmptyState from '@/components/common/EmptyState.vue'

const { campaigns, categories, isLoading, fetchCampaigns, fetchCategories } = useCampaign()

const filters = reactive({ category_id: '', sort: 'newest' })

function loadCampaigns() {
    fetchCampaigns({
        status: 'active',
        category_id: filters.category_id || undefined,
        sort: filters.sort,
    })
}

onMounted(() => {
    fetchCategories()
    loadCampaigns()
})

watch(filters, loadCampaigns)
</script>

<template>
    <div class="mx-auto px-4 sm:px-6 py-10">
        <div class="md:flex md:justify-between "> 
            <h1 class="text-2xl font-bold mb-6">Kampanye Aktif</h1>
    
            <div class="flex flex-wrap gap-4 mb-8">
                <select v-model="filters.category_id" class="border rounded-sm px-4 py-2">
                    <option value="">Semua Kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
    
                <select v-model="filters.sort" class="border rounded-sm px-4 py-2">
                    <option value="newest">Terbaru</option>
                    <option value="popular">Terpopuler</option>
                </select>
            </div>
        </div>

        <SkeletonCard v-if="isLoading" />
        <EmptyState v-else-if="campaigns.length === 0" message="Belum ada kampanye aktif." icon="pi-megaphone" />

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <CampaignCard v-for="campaign in campaigns" :key="campaign.id" :campaign="campaign" />
        </div>
    </div>
</template>
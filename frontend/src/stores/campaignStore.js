import { defineStore } from 'pinia'
import { ref } from 'vue'
import { campaignService } from '@/services/campaignService'
import { campaignImageService } from '@/services/campaignImageService'
import { tierService } from '@/services/tierService'
import { categoryService } from '@/services/categoryService'

export const useCampaignStore = defineStore('campaign', () => {
    const campaigns = ref([])
    const meta = ref({})
    const currentCampaign = ref(null)
    const categories = ref([])
    const isLoading = ref(false)

    async function fetchCampaigns(params = {}) {
        isLoading.value = true
        try {
            const res = await campaignService.getAll(params)
            campaigns.value = res.data.data
            meta.value = res.data.meta || {}
            return res
        } finally {
            isLoading.value = false
        }
    }

    async function fetchOne(id) {
        isLoading.value = true
        try {
            const res = await campaignService.getOne(id)
            currentCampaign.value = res.data.data
            return res
        } finally {
            isLoading.value = false
        }
    }

    async function fetchCategories() {
        try {
            const res = await categoryService.getAll()
            categories.value = res.data.data
        } catch (error) {
            categories.value = []
        }
    }

    async function createCampaignFull({ basicInfo, images, tiers }) {
        isLoading.value = true
        try {
            const createRes = await campaignService.store({
                category_id: basicInfo.category_id,
                title: basicInfo.title,
                description: basicInfo.description,
                target_amount: basicInfo.target_amount,
                deadline: basicInfo.deadline,
                video_url: basicInfo.video_url || null,
            })
            const campaignId = createRes.data.data.id

            if (images.length > 0) {
                await campaignImageService.store(campaignId, images)
            }

            for (const tier of tiers) {
                await tierService.store(campaignId, tier)
            }

            return campaignId
        } finally {
            isLoading.value = false
        }
    }

    async function updateCampaignBasicInfo(id, basicInfo) {
        return campaignService.update(id, {
            category_id: basicInfo.category_id,
            title: basicInfo.title,
            description: basicInfo.description,
            target_amount: basicInfo.target_amount,
            deadline: basicInfo.deadline,
            video_url: basicInfo.video_url || null,
        })
    }

    async function submitCampaign(id) {
        return campaignService.submitToReview(id)
    }

    return {
        campaigns, meta, currentCampaign, categories, isLoading,
        fetchCampaigns, fetchOne, fetchCategories,
        createCampaignFull, updateCampaignBasicInfo, submitCampaign,
    }
})
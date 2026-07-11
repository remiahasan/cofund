import { defineStore } from 'pinia'
import { ref } from 'vue'
import { campaignService } from '@/services/campaignService'
import { categoryService } from '@/services/categoryService'

export const useCampaignStore = defineStore('campaign', () => {
    const campaigns = ref([])
    const myCampaigns = ref([])
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

    async function fetchMyCampaigns(params = {}) {
        isLoading.value = true
        try {
            const res = await campaignService.getMine(params)
            myCampaigns.value = res.data.data
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
            return res
        } catch (error) {
            categories.value = []
        }
    }

    async function createCampaign(formData) {
        isLoading.value = true
        try {
            return await campaignService.store(formData)
        } finally {
            isLoading.value = false
        }
    }

    async function updateCampaign(id, formData) {
        isLoading.value = true
        try {
            return await campaignService.update(id, formData)
        } finally {
            isLoading.value = false
        }
    }

    async function submitCampaign(id) {
        return campaignService.submit(id)
    }

    return {
        campaigns, myCampaigns, meta, currentCampaign, categories, isLoading,
        fetchCampaigns, fetchMyCampaigns, fetchOne, fetchCategories,
        createCampaign, updateCampaign, submitCampaign,
    }
})
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { dashboardService } from '@/services/dashboardService'

export const useDashboardStore = defineStore('dashboard', () => {
    const creatorSummary = ref(null)
    const creatorCampaigns = ref([])
    const fundingChart = ref([])
    const backerSummary = ref(null)
    const isLoading = ref(false)

    async function fetchCreatorDashboard() {
        isLoading.value = true
        try {
            const res = await dashboardService.getCreatorDashboard()
            creatorSummary.value = res.data.data.summary
            creatorCampaigns.value = res.data.data.campaigns
        } finally {
            isLoading.value = false
        }
    }

    async function fetchFundingChart() {
        const res = await dashboardService.getCreatorFundingChart()
        fundingChart.value = res.data.data
    }

    async function fetchBackerDashboard() {
        isLoading.value = true
        try {
            const res = await dashboardService.getBackerDashboard()
            backerSummary.value = res.data.data
        } finally {
            isLoading.value = false
        }
    }

    return {
        creatorSummary, creatorCampaigns, fundingChart, backerSummary, isLoading,
        fetchCreatorDashboard, fetchFundingChart, fetchBackerDashboard,
    }
})
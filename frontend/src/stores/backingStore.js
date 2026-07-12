import { defineStore } from 'pinia'
import { ref } from 'vue'
import { backingService } from '@/services/backingService'

export const useBackingStore = defineStore('backing', () => {
    const myBackings = ref([])
    const isLoading = ref(false)

    async function createBacking(campaignId, payload) {
        isLoading.value = true
        try {
            return await backingService.store(campaignId, payload)
        } finally {
            isLoading.value = false
        }
    }

    async function fetchMyBackings(params = {}) {
        isLoading.value = true
        try {
            const res = await backingService.getMine(params)
            myBackings.value = res.data.data
            return res
        } finally {
            isLoading.value = false
        }
    }

    return { myBackings, isLoading, createBacking, fetchMyBackings }
})
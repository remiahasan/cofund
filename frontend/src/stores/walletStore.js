import { defineStore } from 'pinia'
import { ref } from 'vue'
import { transactionService } from '@/services/transactionService'

export const useWalletStore = defineStore('wallet', () => {
    const transactions = ref([])
    const meta = ref({})
    const isLoading = ref(false)

    async function fetchTransactions(params = {}) {
        isLoading.value = true
        try {
            const res = await transactionService.getMine(params)
            transactions.value = res.data.data
            meta.value = res.data.meta || {}
            return res
        } finally {
            isLoading.value = false
        }
    }

    return { transactions, meta, isLoading, fetchTransactions }
})
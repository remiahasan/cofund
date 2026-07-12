<script setup>
import { onMounted } from 'vue'
import { ref } from 'vue'
import dayjs from 'dayjs'
import { walletService, transactionService } from '@/services/walletService'

const balance = ref(0)
const transactions = ref([])
const isLoading = ref(false)

const typeLabel = { payment: 'Pembayaran', refund: 'Refund', disbursement: 'Pencairan', platform_fee: 'Platform Fee' }
const statusLabel = { pending: 'Menunggu', success: 'Berhasil', failed: 'Gagal' }
const statusColor = {
    pending: 'bg-yellow-100 text-yellow-700', success: 'bg-green-100 text-green-700', failed: 'bg-red-100 text-red-700',
}

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}

onMounted(async () => {
    isLoading.value = true
    try {
        const balRes = await walletService.getBalance()
        balance.value = balRes.data.data.balance

        const txRes = await transactionService.getAll()
        transactions.value = txRes.data.data
    } finally {
        isLoading.value = false
    }
})
</script>

<template>
    <div class="p-4 sm:p-8 max-w-4xl">
        <h1 class="text-2xl font-bold mb-2">Saldo Saya</h1>

        <div class="bg-blue-700 text-white rounded-2xl p-6 my-6">
            <p class="text-sm opacity-80">Saldo Saat Ini</p>
            <p class="text-3xl font-bold mt-1">{{ formatCurrency(balance) }}</p>
        </div>

        <h2 class="text-lg font-semibold mb-3">Riwayat Transaksi</h2>

        <div v-if="isLoading" class="text-center py-16 text-gray-500">Memuat transaksi...</div>
        <div v-else-if="transactions.length === 0" class="text-center py-16 text-gray-500">Belum ada transaksi.</div>

        <div v-else class="flex flex-col gap-2">
            <div v-for="t in transactions" :key="t.id" class="border rounded-xl p-4 flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium">{{ typeLabel[t.type] }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="statusColor[t.status]">{{ statusLabel[t.status] }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ dayjs(t.created_at).format('DD MMM YYYY, HH:mm') }} · Ref: {{ t.reference }}</p>
                </div>
                <div class="font-bold" :class="['refund', 'disbursement'].includes(t.type) ? 'text-green-600' : 'text-gray-800'">
                    {{ ['refund', 'disbursement'].includes(t.type) ? '+' : '-' }}{{ formatCurrency(t.amount) }}
                </div>
            </div>
        </div>
    </div>
</template>
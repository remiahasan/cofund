<script setup>
import { onMounted, reactive, watch } from 'vue'
import { storeToRefs } from 'pinia'
import dayjs from 'dayjs'
import { useAuthStore } from '@/stores/authStore'
import { useWalletStore } from '@/stores/walletStore'

const authStore = useAuthStore()
const walletStore = useWalletStore()
const { transactions, isLoading } = storeToRefs(walletStore)

const filters = reactive({ type: '', from: '', to: '' })

const typeLabel = {
    payment: 'Pembayaran',
    refund: 'Refund',
    disbursement: 'Pencairan',
    platform_fee: 'Platform Fee',
}

const statusLabel = {
    pending: 'Menunggu',
    success: 'Berhasil',
    failed: 'Gagal',
}

const statusColor = {
    pending: 'bg-yellow-100 text-yellow-700',
    success: 'bg-green-100 text-green-700',
    failed: 'bg-red-100 text-red-700',
}

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}

function loadTransactions() {
    walletStore.fetchTransactions({
        type: filters.type || undefined,
        from: filters.from || undefined,
        to: filters.to || undefined,
    })
}

onMounted(() => {
    authStore.fetchProfile() // refresh saldo terbaru
    loadTransactions()
})

watch(filters, loadTransactions)
</script>

<template>
    <div class="p-8 max-w-4xl">
        <h1 class="text-2xl font-bold mb-2">Saldo Saya</h1>

        <div class="bg-blue-700 text-white rounded-2xl p-6 my-6">
            <p class="text-sm opacity-80">Saldo Saat Ini</p>
            <p class="text-3xl font-bold mt-1">{{ formatCurrency(authStore.user?.balance) }}</p>
        </div>

        <h2 class="text-lg font-semibold mb-3">Riwayat Transaksi</h2>

        <div class="flex flex-wrap gap-3 mb-4">
            <select v-model="filters.type" class="border rounded-sm px-3 py-2 text-sm">
                <option value="">Semua Tipe</option>
                <option value="payment">Pembayaran</option>
                <option value="refund">Refund</option>
                <option value="disbursement">Pencairan</option>
                <option value="platform_fee">Platform Fee</option>
            </select>
            <input v-model="filters.from" type="date" class="border rounded-sm px-3 py-2 text-sm" />
            <input v-model="filters.to" type="date" class="border rounded-sm px-3 py-2 text-sm" />
        </div>

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
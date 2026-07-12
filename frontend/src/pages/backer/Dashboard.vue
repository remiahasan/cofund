<script setup>
import { onMounted, computed } from 'vue'
import { storeToRefs } from 'pinia'
import dayjs from 'dayjs'
import { useBackingStore } from '@/stores/backingStore'
import { useAuthStore } from '@/stores/authStore'

const store = useBackingStore()
const authStore = useAuthStore()
const { myBackings, isLoading } = storeToRefs(store)

onMounted(() => {
    store.fetchMyBackings()
    authStore.fetchProfile()
})

const statusLabel = { pending: 'Menunggu Pembayaran', completed: 'Berhasil', refunded: 'Direfund' }
const statusColor = {
    pending: 'bg-yellow-100 text-yellow-700', completed: 'bg-green-100 text-green-700', refunded: 'bg-gray-200 text-gray-700',
}

const totalBacked = computed(() => myBackings.value
    .filter(b => b.status === 'completed')
    .reduce((sum, b) => sum + Number(b.amount), 0))

const totalRefunded = computed(() => myBackings.value
    .filter(b => b.status === 'refunded')
    .reduce((sum, b) => sum + Number(b.amount), 0))

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}
</script>

<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Dashboard Backer</h1>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Saldo Saat Ini</p>
                <p class="text-xl font-bold mt-1">{{ formatCurrency(authStore.user?.balance) }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Total Dana Dibacking</p>
                <p class="text-xl font-bold mt-1">{{ formatCurrency(totalBacked) }}</p>
            </div>
            <div class="border rounded-xl p-4">
                <p class="text-xs text-gray-500">Total Refund Diterima</p>
                <p class="text-xl font-bold mt-1">{{ formatCurrency(totalRefunded) }}</p>
            </div>
        </div>

        <h2 class="text-lg font-semibold mb-3">Riwayat Backing</h2>

        <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat riwayat backing...</div>
        <div v-else-if="myBackings.length === 0" class="text-center py-20 text-gray-500">
            Anda belum pernah melakukan backing ke kampanye manapun.
        </div>

        <div v-else class="flex flex-col gap-4">
            <div v-for="b in myBackings" :key="b.id" class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-semibold">{{ b.campaign?.title || 'Kampanye' }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="statusColor[b.status]">{{ statusLabel[b.status] }}</span>
                    </div>
                    <p class="text-sm text-gray-500">{{ dayjs(b.created_at).format('DD MMM YYYY') }} · {{ b.tier?.name || 'Tanpa reward' }}</p>
                </div>
                <div class="text-right font-bold text-blue-700">{{ formatCurrency(b.amount) }}</div>
            </div>
        </div>
    </div>
</template>
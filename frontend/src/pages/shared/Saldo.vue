<script setup>
import { onMounted, ref } from 'vue'
import dayjs from 'dayjs'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import { useToast } from 'vue-toastification'
import Dialog from 'primevue/dialog'
import { walletService, transactionService } from '@/services/walletService'

const toast = useToast()

const balance = ref(0)
const transactions = ref([])
const isLoading = ref(false)

const showTopupModal = ref(false)
const isSubmittingTopup = ref(false)

const typeLabel = { payment: 'Pembayaran', refund: 'Refund', disbursement: 'Pencairan', platform_fee: 'Platform Fee', topup: 'Top Up' }
const statusLabel = { pending: 'Menunggu', success: 'Berhasil', failed: 'Gagal' }
const statusColor = {
    pending: 'bg-yellow-100 text-yellow-700', success: 'bg-green-100 text-green-700', failed: 'bg-red-100 text-red-700',
}

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}

async function loadWallet() {
    isLoading.value = true
    try {
        const balRes = await walletService.getBalance()
        balance.value = balRes.data.data.balance

        const txRes = await transactionService.getAll()
        transactions.value = txRes.data.data
    } finally {
        isLoading.value = false
    }
}

onMounted(loadWallet)

const topupSchema = yup.object({
    amount: yup.number()
        .typeError('Nominal harus berupa angka')
        .required('Nominal wajib diisi')
        .min(10000, 'Nominal top up minimal Rp10.000'),
})

const { handleSubmit, resetForm } = useForm({ validationSchema: topupSchema })
const { value: amount, errorMessage: amountError } = useField('amount')

const quickAmounts = [50000, 100000, 250000, 500000]

function openTopup() {
    resetForm()
    showTopupModal.value = true
}

function pickQuickAmount(value) {
    amount.value = value
}

const submitTopup = handleSubmit(async (values) => {
    isSubmittingTopup.value = true
    try {
        await walletService.topup(values.amount)
        toast.success('Top up berhasil, saldo Anda sudah bertambah')
        showTopupModal.value = false
        loadWallet()
    } catch (error) {
        toast.error(error.response?.data?.message || 'Top up gagal, silakan coba lagi')
    } finally {
        isSubmittingTopup.value = false
    }
})
</script>

<template>
    <div class="p-4 sm:p-8 max-w-4xl">
        <h1 class="text-2xl font-bold mb-2">Saldo Saya</h1>

        <div
            class="bg-blue-700 text-white rounded-2xl p-6 my-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm opacity-80">Saldo Saat Ini</p>
                <p class="text-3xl font-bold mt-1">{{ formatCurrency(balance) }}</p>
            </div>
            <button @click="openTopup"
                class="bg-white text-blue-700 px-5 py-2.5 rounded-sm font-semibold self-start sm:self-auto">
                + Top Up Saldo
            </button>
        </div>
        <h2 class="text-lg font-semibold mb-3">Riwayat Transaksi</h2>
        <div v-if="isLoading" class="text-center py-16 text-gray-500">Memuat transaksi...</div>
        <div v-else-if="transactions.length === 0" class="text-center py-16 text-gray-500">Belum ada transaksi.</div>
        <div v-else class="flex flex-col gap-2">
            <div v-for="t in transactions" :key="t.id" class="border rounded-xl p-4 flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium">{{ typeLabel[t.type] || t.type }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="statusColor[t.status]">{{
                            statusLabel[t.status] }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ dayjs(t.created_at).format('DD MMM YYYY, HH:mm') }} · Ref:
                        {{ t.reference }}</p>
                </div>
                <div class="font-bold"
                    :class="['refund', 'disbursement', 'topup'].includes(t.type) ? 'text-green-600' : 'text-gray-800'">
                    {{ ['refund', 'disbursement', 'topup'].includes(t.type) ? '+' : '-' }}{{ formatCurrency(t.amount) }}
                </div>
            </div>
        </div>
        <Dialog :visible="showTopupModal" @update:visible="val => showTopupModal = val" modal header="Top Up Saldo"
            class="w-full max-w-sm !bg-gray-200 !rounded-2xl py-5 px-5 overflow-hidden">
            <form @submit.prevent="submitTopup" class="flex flex-col gap-4 bg-gray-200" novalidate>
                <div>
                    <label class="font-medium text-sm">Nominal Top Up</label>
                    <input v-model="amount" type="number" min="10000" placeholder="Minimal Rp10.000"
                        class="w-full border rounded-sm px-3 py-2 mt-1" />
                    <span v-if="amountError" class="text-red-500 text-xs">{{ amountError }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button v-for="qa in quickAmounts" :key="qa" type="button" @click="pickQuickAmount(qa)"
                        class="border rounded-sm px-3 py-1.5 text-xs font-semibold text-gray-600 hover:border-blue-700 hover:text-blue-700">
                        {{ formatCurrency(qa) }}
                    </button>
                </div>
                <button type="submit" :disabled="isSubmittingTopup"
                    class="bg-blue-700 text-white py-2 rounded-sm font-semibold disabled:bg-gray-300">
                    {{ isSubmittingTopup ? 'Memproses...' : 'Top Up Sekarang' }}
                </button>
            </form>
        </Dialog>
    </div>
</template>
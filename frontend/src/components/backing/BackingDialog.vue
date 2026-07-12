<script setup>
import Dialog from 'primevue/dialog'

const props = defineProps({
    visible: { type: Boolean, required: true },
    phase: { type: String, required: true },
    campaign: { type: Object, required: true },
    availableTiers: { type: Array, required: true },
    selectedOption: { type: [Object, String, null], default: null },
    freeAmount: { type: [Number, String, null], default: null },
    validationError: { type: String, default: '' },
    errorMessage: { type: String, default: '' },
    finalAmount: { type: Number, default: 0 },
    freeAmountKey: { type: String, required: true },
})

const emit = defineEmits([
    'update:visible', 'update:freeAmount', 'select-tier', 'select-free',
    'next', 'back', 'confirm', 'retry', 'done',
])

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal :closable="phase !== 'processing'"
        :header="`Backing: ${campaign?.title || ''}`" class="w-full max-w-lg">

        <div v-if="phase === 'select'" class="flex flex-col gap-3">
            <p class="text-sm text-gray-500">Pilih tier reward atau masukkan nominal bebas (tanpa reward).</p>

            <div v-for="tier in availableTiers" :key="tier.id"
                @click="$emit('select-tier', tier)"
                class="border rounded-xl p-4 cursor-pointer transition"
                :class="selectedOption === tier ? 'border-blue-700 ring-1 ring-blue-700' : 'border-gray-200'">
                <div class="flex justify-between items-start">
                    <h4 class="font-semibold">{{ tier.name }}</h4>
                    <span class="font-bold text-blue-700">{{ formatCurrency(tier.min_amount) }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ tier.reward_description }}</p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ tier.quota === 0 ? 'Kuota tidak terbatas' : `Sisa ${tier.remaining_quota} slot` }}
                </p>
            </div>

            <div @click="$emit('select-free')" class="border rounded-xl p-4 cursor-pointer transition"
                :class="selectedOption === freeAmountKey ? 'border-blue-700 ring-1 ring-blue-700' : 'border-gray-200'">
                <h4 class="font-semibold">Nominal Bebas (tanpa reward)</h4>
                <input v-if="selectedOption === freeAmountKey"
                    :value="freeAmount" @input="$emit('update:freeAmount', $event.target.value)" @click.stop
                    type="number" min="10000" placeholder="Minimal Rp10.000"
                    class="w-full border rounded-sm px-3 py-2 mt-2" />
            </div>

            <span v-if="validationError" class="text-red-500 text-xs">{{ validationError }}</span>

            <button @click="$emit('next')" class="bg-blue-700 text-white py-2 rounded-sm font-semibold mt-2">Lanjut</button>
        </div>

        <div v-else-if="phase === 'confirm'" class="flex flex-col gap-4">
            <h3 class="font-semibold">Ringkasan Backing</h3>
            <div class="border rounded-xl p-4 text-sm flex flex-col gap-1">
                <div class="flex justify-between"><span class="text-gray-500">Kampanye</span><span class="font-medium">{{ campaign.title }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tier</span><span class="font-medium">{{ selectedOption === freeAmountKey ? 'Tanpa reward' : selectedOption.name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Nominal</span><span class="font-bold text-blue-700">{{ formatCurrency(finalAmount) }}</span></div>
            </div>
            <p class="text-xs text-gray-400">Dana akan ditahan di escrow platform hingga kampanye berhasil atau gagal.</p>
            <div class="flex gap-2">
                <button @click="$emit('back')" class="border flex-1 py-2 rounded-sm font-semibold">Kembali</button>
                <button @click="$emit('confirm')" class="bg-blue-700 text-white flex-1 py-2 rounded-sm font-semibold">Konfirmasi & Bayar</button>
            </div>
        </div>

        <div v-else-if="phase === 'processing'" class="text-center py-10">
            <p class="text-gray-500">Memproses pembayaran...</p>
        </div>

        <div v-else-if="phase === 'success'" class="text-center py-6 flex flex-col gap-3 items-center">
            <div class="text-green-600 text-4xl">✓</div>
            <h3 class="font-semibold">Backing Berhasil!</h3>
            <p class="text-sm text-gray-500">Terima kasih telah mendukung kampanye ini. Dana Anda sudah masuk escrow.</p>
            <button @click="$emit('done')" class="bg-blue-700 text-white px-6 py-2 rounded-sm font-semibold mt-2">Selesai</button>
        </div>

        <div v-else-if="phase === 'failed'" class="text-center py-6 flex flex-col gap-3 items-center">
            <div class="text-red-600 text-4xl">✕</div>
            <h3 class="font-semibold">Backing Gagal</h3>
            <p class="text-sm text-gray-500">{{ errorMessage }}</p>
            <button @click="$emit('retry')" class="bg-blue-700 text-white px-6 py-2 rounded-sm font-semibold mt-2">Coba Lagi</button>
        </div>
    </Dialog>
</template>
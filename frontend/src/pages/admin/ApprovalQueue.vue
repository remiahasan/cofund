<script setup>
import { onMounted, ref } from 'vue'
import { useToast } from 'vue-toastification'
import dayjs from 'dayjs'
import { adminService } from '@/services/adminService'

const toast = useToast()
const campaigns = ref([])
const isLoading = ref(false)

const rejectDialogFor = ref(null)
const rejectReason = ref('')

async function loadPending() {
    isLoading.value = true
    try {
        const res = await adminService.getPendingCampaigns()
        campaigns.value = res.data.data
    } finally {
        isLoading.value = false
    }
}

onMounted(loadPending)

async function approve(id) {
    try {
        await adminService.approveCampaign(id)
        toast.success('Kampanye disetujui')
        loadPending()
    } catch (error) {
        toast.error(error.response?.data?.message || 'Gagal menyetujui kampanye')
    }
}

function openReject(id) {
    rejectDialogFor.value = id
    rejectReason.value = ''
}

async function confirmReject() {
    if (!rejectReason.value.trim()) {
        toast.error('Catatan alasan penolakan wajib diisi')
        return
    }
    try {
        await adminService.rejectCampaign(rejectDialogFor.value, rejectReason.value)
        toast.success('Kampanye ditolak')
        rejectDialogFor.value = null
        loadPending()
    } catch (error) {
        toast.error(error.response?.data?.message || 'Gagal menolak kampanye')
    }
}
</script>

<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Approval Queue</h1>

        <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat...</div>
        <div v-else-if="campaigns.length === 0" class="text-center py-20 text-gray-500">Tidak ada kampanye menunggu review.</div>

        <div v-else class="flex flex-col gap-4">
            <div v-for="c in campaigns" :key="c.id" class="border rounded-xl p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">{{ c.title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">oleh {{ c.user?.name || '-' }} · diajukan {{ dayjs(c.created_at).format('DD MMM YYYY') }}</p>
                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ c.description }}</p>
                    </div>
                    <router-link :to="{ name: 'campaign.detail', params: { id: c.id } }" class="text-blue-700 text-sm font-semibold whitespace-nowrap">Lihat Detail</router-link>
                </div>

                <div class="flex gap-2 mt-4">
                    <button @click="approve(c.id)" class="bg-green-600 text-white px-4 py-2 rounded-sm text-sm font-semibold">Approve</button>
                    <button @click="openReject(c.id)" class="bg-red-600 text-white px-4 py-2 rounded-sm text-sm font-semibold">Reject</button>
                </div>

                <div v-if="rejectDialogFor === c.id" class="mt-4 border-t pt-4">
                    <label class="text-sm font-medium">Catatan Alasan Penolakan</label>
                    <textarea v-model="rejectReason" rows="3" class="w-full border rounded-sm px-3 py-2 mt-1" placeholder="Wajib diisi..."></textarea>
                    <div class="flex gap-2 mt-2">
                        <button @click="confirmReject" class="bg-red-600 text-white px-4 py-2 rounded-sm text-sm font-semibold">Kirim Penolakan</button>
                        <button @click="rejectDialogFor = null" class="border px-4 py-2 rounded-sm text-sm font-semibold">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
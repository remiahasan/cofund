<script setup>
import { onMounted, ref, reactive, watch } from 'vue'
import { useToast } from 'vue-toastification'
import dayjs from 'dayjs'
import { adminService } from '@/services/adminService'
import EmptyState from '@/components/common/EmptyState.vue'
import SkeletonRow from '@/components/common/SkeletonRow.vue'

const toast = useToast()
const campaigns = ref([])
const isLoading = ref(false)
const filters = reactive({ status: '' })

const expandedId = ref(null)
const expandedDetail = ref(null)
const isLoadingDetail = ref(false)
const confirmForceFailId = ref(null)

const statusLabel = { draft: 'Draft', review: 'Review', active: 'Aktif', success: 'Berhasil', failed: 'Gagal' }
const statusColor = {
    draft: 'bg-gray-200 text-gray-700', review: 'bg-yellow-100 text-yellow-700',
    active: 'bg-blue-100 text-blue-700', success: 'bg-green-100 text-green-700', failed: 'bg-red-100 text-red-700',
}
const backingStatusLabel = { pending: 'Menunggu', completed: 'Berhasil', refunded: 'Direfund' }

async function loadCampaigns() {
    isLoading.value = true
    try {
        const res = await adminService.getAllCampaigns(filters.status || undefined)
        campaigns.value = res.data.data
    } finally {
        isLoading.value = false
    }
}

onMounted(loadCampaigns)
watch(filters, loadCampaigns)

async function toggleExpand(campaign) {
    if (expandedId.value === campaign.id) {
        expandedId.value = null
        return
    }
    expandedId.value = campaign.id
    isLoadingDetail.value = true
    try {
        const res = await adminService.getCampaignDetail(campaign.id)
        expandedDetail.value = res.data.data
    } catch (error) {
        expandedDetail.value = null
    } finally {
        isLoadingDetail.value = false
    }
}

async function confirmForceFail() {
    try {
        await adminService.forceFailCampaign(confirmForceFailId.value)
        toast.success('Kampanye di-force-fail, refund otomatis diproses ke semua backer')
        confirmForceFailId.value = null
        loadCampaigns()
    } catch (error) {
        toast.error(error.response?.data?.message || 'Gagal memproses force-fail')
    }
}

function formatCurrency(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID')
}
</script>

<template>
    <div class="p-4 sm:p-8">
        <h1 class="text-2xl font-bold mb-6">Manajemen Kampanye</h1>

        <div class="flex flex-wrap gap-3 mb-4">
            <select v-model="filters.status" class="border rounded-sm px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="review">Review</option>
                <option value="active">Aktif</option>
                <option value="success">Berhasil</option>
                <option value="failed">Gagal</option>
            </select>
        </div>

        <SkeletonRow v-if="isLoading" />
        <EmptyState v-else-if="campaigns.length === 0" message="Tidak ada kampanye ditemukan." icon="pi-megaphone" />

        <div v-else class="flex flex-col gap-3">
            <div v-for="c in campaigns" :key="c.id" class="border rounded-xl p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold">{{ c.title }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="statusColor[c.status]">{{ statusLabel[c.status] }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">oleh {{ c.creator?.name || '-' }} · deadline {{ dayjs(c.deadline).format('DD MMM YYYY') }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ formatCurrency(c.collected_amount) }} / {{ formatCurrency(c.target_amount) }} · {{ c.total_backers }} backer</p>
                    </div>

                    <div class="flex gap-2">
                        <button @click="toggleExpand(c)" class="border px-3 py-1.5 rounded-sm text-sm font-semibold">
                            {{ expandedId === c.id ? 'Tutup' : 'Lihat Detail' }}
                        </button>
                        <button v-if="c.status === 'active'" @click="confirmForceFailId = c.id"
                            class="bg-red-600 text-white px-3 py-1.5 rounded-sm text-sm font-semibold">
                            Force-Fail
                        </button>
                    </div>
                </div>

                <div v-if="expandedId === c.id" class="mt-4 border-t pt-4">
                    <div v-if="isLoadingDetail" class="text-sm text-gray-400">Memuat detail...</div>
                    <template v-else-if="expandedDetail">
                        <EmptyState v-if="!expandedDetail.backings?.length" message="Belum ada backing untuk kampanye ini." icon="pi-wallet" />
                        <div v-else class="flex flex-col gap-2">
                            <div v-for="b in expandedDetail.backings" :key="b.id" class="flex justify-between text-sm border-b py-2">
                                <div>
                                    <span class="font-medium">{{ b.user?.name || '-' }}</span>
                                    <span class="text-gray-400 ml-2">{{ dayjs(b.created_at).format('DD MMM YYYY') }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full ml-2 bg-gray-100 text-gray-600">{{ backingStatusLabel[b.status] }}</span>
                                </div>
                                <span class="font-semibold text-blue-700">{{ formatCurrency(b.amount) }}</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div v-if="confirmForceFailId === c.id" class="mt-4 border-t pt-4 bg-red-50 rounded-lg p-3">
                    <p class="text-sm text-red-700">Yakin ingin force-fail kampanye ini? Semua backing yang sudah completed akan otomatis direfund ke backer.</p>
                    <div class="flex gap-2 mt-3">
                        <button @click="confirmForceFail" class="bg-red-600 text-white px-4 py-1.5 rounded-sm text-sm font-semibold">Ya, Force-Fail</button>
                        <button @click="confirmForceFailId = null" class="border px-4 py-1.5 rounded-sm text-sm font-semibold">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
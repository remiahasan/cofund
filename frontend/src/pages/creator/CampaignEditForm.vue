<script setup>
import { computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useCampaign } from '@/composables/useCampaign'
import { useCampaignForm } from '@/composables/useCampaignForm'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const campaignId = route.params.id
const { categories, currentCampaign, fetchCategories } = useCampaign()

const {
    step, isSubmitting, isLoadingInitial, basicInfo, images, imagePreviews,
    existingImages, tiers, errors,
    addTier, removeTier, addImages, removeImage, removeExistingImage,
    nextStep, prevStep, submitCampaign,
} = useCampaignForm({ mode: 'edit', campaignId })

onMounted(fetchCategories)

const isEditable = computed(() => currentCampaign.value?.status === 'draft')

watch(currentCampaign, (val) => {
    if (val && val.status !== 'draft') {
        toast.error('Kampanye hanya bisa diedit selama masih berstatus draft')
        router.push({ name: 'dashboard.creator' })
    }
}, { immediate: true })

function onImageChange(e) {
    addImages(e.target.files)
    e.target.value = ''
}

async function handleSubmit() {
    try {
        await submitCampaign()
        toast.success('Perubahan kampanye berhasil disimpan')
        router.push({ name: 'dashboard.creator' })
    } catch (error) {
        toast.error(error.response?.data?.message || 'Gagal menyimpan perubahan')
    }
}
</script>

<template>
    <div v-if="isLoadingInitial" class="text-center py-20 text-gray-500">Memuat data kampanye...</div>
    <div v-else-if="isEditable" class="max-w-4xl mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-2">Edit Kampanye</h1>
        <p class="text-gray-500 mb-8">Langkah {{ step }} dari 3</p>
        <div v-if="step === 1" class="flex flex-col gap-4">
            <div>
                <label class="font-medium">Judul Kampanye</label>
                <input v-model="basicInfo.title" maxlength="100" class="w-full border rounded-sm px-4 py-2 mt-1" />
                <span v-if="errors.basicInfo.title" class="text-red-500 text-xs">{{ errors.basicInfo.title }}</span>
            </div>
            <div>
                <label class="font-medium">Kategori</label>
                <select v-model="basicInfo.category_id" class="w-full border rounded-sm px-4 py-2 mt-1">
                    <option value="">Pilih kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <span v-if="errors.basicInfo.category_id" class="text-red-500 text-xs">{{ errors.basicInfo.category_id }}</span>
            </div>
            <div>
                <label class="font-medium">Deskripsi</label>
                <textarea v-model="basicInfo.description" rows="6" class="w-full border rounded-sm px-4 py-2 mt-1"></textarea>
                <span v-if="errors.basicInfo.description" class="text-red-500 text-xs">{{ errors.basicInfo.description }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-medium">Target Dana (Rp)</label>
                    <input v-model.number="basicInfo.target_amount" type="number" min="100000" class="w-full border rounded-sm px-4 py-2 mt-1" />
                    <span v-if="errors.basicInfo.target_amount" class="text-red-500 text-xs">{{ errors.basicInfo.target_amount }}</span>
                </div>
                <div>
                    <label class="font-medium">Deadline</label>
                    <input v-model="basicInfo.deadline" type="date" class="w-full border rounded-sm px-4 py-2 mt-1" />
                    <span v-if="errors.basicInfo.deadline" class="text-red-500 text-xs">{{ errors.basicInfo.deadline }}</span>
                </div>
            </div>
            <div>
                <label class="font-medium">Video URL (opsional)</label>
                <input v-model="basicInfo.video_url" class="w-full border rounded-sm px-4 py-2 mt-1" />
                <span v-if="errors.basicInfo.video_url" class="text-red-500 text-xs">{{ errors.basicInfo.video_url }}</span>
            </div>
            <div class="pt-4 flex justify-end">
                <button @click="nextStep" type="button" class="bg-blue-700 text-white px-6 py-2 rounded-sm font-semibold">Lanjut</button>
            </div>
        </div>
        <div v-if="step === 2" class="flex flex-col gap-8">
            <div>
                <label class="font-medium">Foto Kampanye (1-5 foto)</label>
                <div class="flex flex-wrap gap-3 mt-3" v-if="existingImages.length">
                    <div v-for="img in existingImages" :key="img.id" class="relative">
                        <img :src="'http://localhost:8000' + img.url" class="w-24 h-24 object-cover rounded-lg border" />
                        <button @click="removeExistingImage(img.id)" type="button" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 text-xs">✕</button>
                    </div>
                </div>
                <input type="file" accept="image/*" multiple @change="onImageChange" class="mt-3 block" />
                <span v-if="errors.images" class="text-red-500 text-xs">{{ errors.images }}</span>
                <div class="flex flex-wrap gap-3 mt-3" v-if="imagePreviews.length">
                    <div v-for="(src, i) in imagePreviews" :key="i" class="relative">
                        <img :src="src" class="w-24 h-24 object-cover rounded-lg border" />
                        <button @click="removeImage(i)" type="button" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 text-xs">✕</button>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="font-medium">Tier Reward (minimal 1)</label>
                    <button @click="addTier" type="button" class="text-blue-700 text-sm font-semibold">+ Tambah Tier</button>
                </div>
                <div v-for="(tier, i) in tiers" :key="tier.id ?? `new-${i}`" class="rounded-xl p-4 mb-3 flex flex-col gap-2 bg-gray-200">
                    <div class="flex justify-between">
                        <span class="font-semibold text-sm">Tier {{ i + 1 }}<span v-if="tier.id" class="text-gray-400 font-normal"> (tersimpan)</span></span>
                        <button v-if="tiers.length > 1" @click="removeTier(i)" type="button" class="text-red-500 text-xs">Hapus</button>
                    </div>
                    <input v-model="tier.name" placeholder="Nama tier" class="w-full border rounded-sm border-gray-500 px-3 py-2" />
                    <span v-if="errors.tiers[i]?.name" class="text-red-500 text-xs">{{ errors.tiers[i].name }}</span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <input v-model.number="tier.min_amount" type="number" placeholder="Min. nominal (Rp)" class="w-full border border-gray-500 rounded-sm border-gray-300 px-3 py-2" />
                            <span v-if="errors.tiers[i]?.min_amount" class="text-red-500 text-xs">{{ errors.tiers[i].min_amount }}</span>
                        </div>
                        <div>
                            <input v-model.number="tier.quota" type="number" placeholder="Kuota (0 = tidak terbatas)" class="w-full border border-gray-500 rounded-sm px-3 py-2" />
                            <span v-if="errors.tiers[i]?.quota" class="text-red-500 text-xs">{{ errors.tiers[i].quota }}</span>
                        </div>
                    </div>
                    <textarea v-model="tier.reward_description" rows="2" placeholder="Deskripsi reward" class="w-full border border-gray-500 rounded-sm px-3 py-2"></textarea>
                    <span v-if="errors.tiers[i]?.reward_description" class="text-red-500 text-xs">{{ errors.tiers[i].reward_description }}</span>
                </div>
            </div>
            <div class="flex flex-col gap-2 md:flex-row md:justify-between pt-2">
                <button @click="prevStep" type="button" class="border px-6 py-2 rounded-sm font-semibold">Kembali</button>
                <button @click="nextStep" type="button" class="bg-blue-700 text-white px-6 py-2 rounded-sm font-semibold">Lanjut ke Preview</button>
            </div>
        </div>
        <div v-if="step === 3" class="flex flex-col gap-6">
            <h2 class="text-xl font-semibold">Preview Perubahan</h2>
            <div class="flex gap-3 flex-wrap">
                <img v-for="img in existingImages" :key="img.id" :src="'http://localhost:8000' + img.url" class="w-28 h-28 object-cover rounded-lg border" />
                <img v-for="(src, i) in imagePreviews" :key="i" :src="src" class="w-28 h-28 object-cover rounded-lg border" />
            </div>
            <div>
                <h3 class="text-2xl font-bold">{{ basicInfo.title }}</h3>
                <p class="text-gray-500 mt-1">Target: Rp{{ Number(basicInfo.target_amount || 0).toLocaleString('id-ID') }} · Deadline: {{ basicInfo.deadline }}</p>
                <p class="text-gray-700 mt-4 whitespace-pre-line">{{ basicInfo.description }}</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Tier ({{ tiers.length }})</h4>
                <ul class="list-disc pl-5 text-sm text-gray-700">
                    <li v-for="(tier, i) in tiers" :key="i">{{ tier.name }} — Rp{{ Number(tier.min_amount || 0).toLocaleString('id-ID') }}</li>
                </ul>
            </div>
            <div class="flex flex-col gap-2 md:flex-row md:justify-between pt-2">
                <button @click="prevStep" type="button" class="border px-6 py-2 rounded-sm font-semibold">Kembali</button>
                <button @click="handleSubmit" :disabled="isSubmitting" type="button" class="bg-blue-700 text-white px-6 py-2 rounded-sm font-semibold disabled:bg-gray-300">
                    {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </div>
    </div>
</template>
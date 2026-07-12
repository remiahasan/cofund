import { reactive, ref, computed } from 'vue'
import * as yup from 'yup'
import dayjs from 'dayjs'
import { useCampaignStore } from '@/stores/campaignStore'
import { tierService } from '@/services/tierService'
import { campaignImageService } from '@/services/campaignImageService'

const basicInfoSchema = yup.object({
    title: yup.string().required('Judul wajib diisi').max(100, 'Judul maksimal 100 karakter'),
    category_id: yup.string().required('Kategori wajib dipilih'),
    description: yup.string().required('Deskripsi wajib diisi'),
    target_amount: yup.number()
        .typeError('Target dana harus berupa angka')
        .required('Target dana wajib diisi')
        .min(100000, 'Target dana minimal Rp100.000'),
    deadline: yup.date()
        .typeError('Deadline wajib diisi')
        .required('Deadline wajib diisi')
        .min(dayjs().add(7, 'day').startOf('day').toDate(), 'Deadline minimal 7 hari dari sekarang'),
    video_url: yup.string().url('Format URL tidak valid').nullable().notRequired(),
})

const tierSchema = yup.object({
    name: yup.string().required('Nama tier wajib diisi'),
    minimum_amount: yup.number().typeError('Harus angka').required('Nominal wajib diisi').min(0),
    quota: yup.number().typeError('Harus angka').integer().min(0).required('Kuota wajib diisi (0 = tidak terbatas)'),
    reward_description: yup.string().required('Deskripsi reward wajib diisi'),
})

export function useCampaignForm({ mode = 'create', campaignId = null } = {}) {
    const store = useCampaignStore()
    const currentCampaignId = ref(campaignId)

    const step = ref(1)
    const isSubmitting = ref(false)
    const isLoadingInitial = ref(mode === 'edit')

    const basicInfo = reactive({
        title: '', category_id: '', description: '',
        target_amount: null, deadline: null, video_url: '',
    })

    const images = ref([])
    const imagePreviews = computed(() => images.value.map(f => URL.createObjectURL(f)))
    const existingImages = ref([])
    const removedImageIds = ref([])

    const tiers = reactive([{ name: '', minimum_amount: null, quota: 0, reward_description: '' }])
    const removedTierIds = ref([])

    const errors = reactive({ basicInfo: {}, images: '', tiers: [] })

    async function loadExisting() {
        if (mode !== 'edit' || !currentCampaignId.value) return
        isLoadingInitial.value = true
        try {
            const res = await store.fetchOne(currentCampaignId.value)
            const c = res.data.data
            basicInfo.title = c.title
            basicInfo.category_id = c.category?.id ?? ''
            basicInfo.description = c.description
            basicInfo.target_amount = Number(c.target_amount)
            basicInfo.deadline = dayjs(c.deadline).format('YYYY-MM-DD')
            basicInfo.video_url = c.video_url || ''
            existingImages.value = c.images || []
            if (c.tiers?.length) {
                tiers.splice(0, tiers.length, ...c.tiers.map(t => ({
                    id: t.id, name: t.name, minimum_amount: Number(t.minimum_amount),
                    quota: t.quota, reward_description: t.reward_description,
                })))
            }
        } finally {
            isLoadingInitial.value = false
        }
    }

    if (mode === 'edit') loadExisting()

    async function validateBasicInfo() {
        errors.basicInfo = {}
        try {
            await basicInfoSchema.validate(basicInfo, { abortEarly: false })
            return true
        } catch (err) {
            err.inner.forEach(e => { errors.basicInfo[e.path] = e.message })
            return false
        }
    }

    function validateImages() {
        const total = existingImages.value.length + images.value.length
        if (total < 1) { errors.images = 'Minimal 1 foto kampanye wajib diunggah'; return false }
        if (total > 5) { errors.images = 'Maksimal 5 foto kampanye'; return false }
        errors.images = ''
        return true
    }

    async function validateTiers() {
        errors.tiers = []
        let valid = tiers.length >= 1
        for (let i = 0; i < tiers.length; i++) {
            try {
                await tierSchema.validate(tiers[i], { abortEarly: false })
                errors.tiers[i] = {}
            } catch (err) {
                valid = false
                const tierErrors = {}
                err.inner.forEach(e => { tierErrors[e.path] = e.message })
                errors.tiers[i] = tierErrors
            }
        }
        return valid
    }

    function addTier() {
        tiers.push({ name: '', minimum_amount: null, quota: 0, reward_description: '' })
    }

    function removeTier(index) {
        const t = tiers[index]
        if (t.id) removedTierIds.value.push(t.id)
        if (tiers.length > 1) tiers.splice(index, 1)
    }

    function addImages(fileList) {
        const remainingSlots = 5 - existingImages.value.length
        images.value = [...images.value, ...Array.from(fileList)].slice(0, Math.max(remainingSlots, 0))
    }

    function removeImage(index) {
        images.value.splice(index, 1)
    }

    function removeExistingImage(imageId) {
        removedImageIds.value.push(imageId)
        existingImages.value = existingImages.value.filter(i => i.id !== imageId)
    }

    async function nextStep() {
        if (step.value === 1 && !(await validateBasicInfo())) return
        if (step.value === 2) {
            const okImages = validateImages()
            const okTiers = await validateTiers()
            if (!okImages || !okTiers) return
        }
        step.value++
    }

    function prevStep() {
        if (step.value > 1) step.value--
    }

    async function submitCampaign() {
        isSubmitting.value = true
        try {
            let resultId = currentCampaignId.value

            if (mode === 'create') {
                resultId = await store.createCampaignFull({ basicInfo, images: images.value, tiers })
                currentCampaignId.value = resultId
            } else {
                await store.updateCampaignBasicInfo(resultId, basicInfo)

                if (images.value.length > 0) {
                    await campaignImageService.store(resultId, images.value)
                }
                for (const imgId of removedImageIds.value) {
                    await campaignImageService.destroy(resultId, imgId)
                }
                for (const tierId of removedTierIds.value) {
                    await tierService.destroy(tierId)
                }
                for (const tier of tiers) {
                    if (tier.id) {
                        await tierService.update(tier.id, tier)
                    } else {
                        await tierService.store(resultId, tier)
                    }
                }
            }

            return resultId
        } finally {
            isSubmitting.value = false
        }
    }

    return {
        step, isSubmitting, isLoadingInitial, basicInfo, images, imagePreviews,
        existingImages, tiers, errors,
        addTier, removeTier, addImages, removeImage, removeExistingImage,
        nextStep, prevStep, submitCampaign,
    }
}
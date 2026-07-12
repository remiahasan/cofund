import { reactive, ref, computed } from 'vue'
import { useBackingStore } from '@/stores/backingStore'

const FREE_AMOUNT = 'free'
const MIN_BACKING = 10000

export function useBackingFlow(campaign) {
    const store = useBackingStore()

    const isOpen = ref(false)
    const phase = ref('select')
    const errorMessage = ref('')

    const selectedOption = ref(null)
    const freeAmount = ref(null)
    const validationError = ref('')

    const availableTiers = computed(() => {
        return (campaign.value?.tiers || []).filter(t => t.quota === 0 || t.remaining_quota > 0)
    })

    const finalAmount = computed(() => {
        if (selectedOption.value === FREE_AMOUNT) return Number(freeAmount.value) || 0
        if (selectedOption.value) return Number(selectedOption.value.min_amount)
        return 0
    })

    function open() {
        isOpen.value = true
        phase.value = 'select'
        selectedOption.value = null
        freeAmount.value = null
        validationError.value = ''
        errorMessage.value = ''
    }

    function close() {
        isOpen.value = false
    }

    function selectTier(tier) {
        selectedOption.value = tier
    }

    function selectFree() {
        selectedOption.value = FREE_AMOUNT
    }

    function validateAndGoToConfirm() {
        validationError.value = ''
        if (!selectedOption.value) {
            validationError.value = 'Pilih tier atau masukkan nominal bebas'
            return
        }
        if (finalAmount.value < MIN_BACKING) {
            validationError.value = `Nominal minimum backing adalah Rp${MIN_BACKING.toLocaleString('id-ID')}`
            return
        }
        phase.value = 'confirm'
    }

    async function confirmBacking() {
        phase.value = 'processing'
        try {
            const payload = {
                amount: finalAmount.value,
                tier_id: selectedOption.value === FREE_AMOUNT ? null : selectedOption.value.id,
            }
            await store.createBacking(campaign.value.id, payload)
            phase.value = 'success'
        } catch (error) {
            errorMessage.value = error.response?.data?.message || 'Backing gagal, silakan coba lagi'
            phase.value = 'failed'
        }
    }

    return {
        isOpen, phase, errorMessage, selectedOption, freeAmount, validationError,
        availableTiers, finalAmount, FREE_AMOUNT,
        open, close, selectTier, selectFree, validateAndGoToConfirm, confirmBacking,
    }
}
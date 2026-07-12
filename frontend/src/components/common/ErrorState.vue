<script setup>
defineProps({
    message: { type: String, default: 'Terjadi kesalahan. Silakan coba lagi.' },
})
const emit = defineEmits(['retry'])
</script>

<template>
    <div class="text-center py-16 text-red-500 flex flex-col items-center gap-3">
        <i class="pi pi-exclamation-triangle text-3xl"></i>
        <p class="text-sm">{{ message }}</p>
        <button v-if="$attrs.onRetry !== undefined" @click="emit('retry')" class="border px-4 py-1.5 rounded-sm text-sm font-semibold text-gray-700">
            Coba Lagi
        </button>
    </div>
</template>
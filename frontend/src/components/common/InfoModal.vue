<script setup>
import Dialog from 'primevue/dialog'

defineProps({
    visible: { type: Boolean, required: true },
    title: { type: String, default: '' },
    message: { type: String, default: '' },
    buttonText: { type: String, default: 'OK' },
    icon: { type: String, default: 'pi-check-circle' },
    iconColor: { type: String, default: 'text-green-600' },
})

const emit = defineEmits(['update:visible', 'confirm'])

function handleConfirm() {
    emit('confirm')
    emit('update:visible', false)
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal :closable="false" class="w-full max-w-sm !bg-gray-200 !rounded-2xl py-5 px-5 overflow-hidden">
        <div class="text-center py-4 flex flex-col items-center gap-3">
            <i :class="['pi', icon, iconColor, 'text-4xl']"></i>
            <h3 class="font-semibold text-lg">{{ title }}</h3>
            <p class="text-sm text-gray-500">{{ message }}</p>
            <button @click="handleConfirm" class="bg-blue-700 text-white px-6 py-2 rounded-sm font-semibold mt-2 w-full">
                {{ buttonText }}
            </button>
        </div>
    </Dialog>
</template>
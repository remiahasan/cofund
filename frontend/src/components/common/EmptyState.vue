<script setup>
defineProps({
    message: { type: String, default: 'Belum ada data.' },
    icon: { type: String, default: 'pi-inbox' },
})
</script>

<template>
    <div class="text-center py-16 text-gray-400 flex flex-col items-center gap-2">
        <i :class="['pi', icon, 'text-3xl']"></i>
        <p class="text-sm">{{ message }}</p>
    </div>
</template>
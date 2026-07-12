<script setup>
defineProps({
    count: { type: Number, default: 4 },
})
</script>

<template>
    <div class="flex flex-col gap-4">
        <div v-for="i in count" :key="i" class="border rounded-xl p-4 animate-pulse flex flex-col gap-2">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            <div class="h-2 bg-gray-200 rounded w-full mt-1"></div>
        </div>
    </div>
</template>
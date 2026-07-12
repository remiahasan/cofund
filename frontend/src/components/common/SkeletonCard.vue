<script setup>
defineProps({
    count: { type: Number, default: 6 },
})
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in count" :key="i" class="rounded-2xl overflow-hidden shadow-sm animate-pulse">
            <div class="w-full aspect-video bg-gray-200"></div>
            <div class="p-4 flex flex-col gap-2">
                <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-2 bg-gray-200 rounded w-full mt-1"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
    </div>
</template>
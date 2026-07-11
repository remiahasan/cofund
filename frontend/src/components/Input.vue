<template>
    <div class="flex flex-col gap-1">
        <label v-if="label">{{ label }}</label>
        <input
            :type="type"
            :name="name"
            class="w-full border rounded-sm px-4 py-2 focus:outline-none focus:ring-1"
            :class="error ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-600'"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        <span v-if="error" class="text-red-500 text-xs">{{ error }}</span>
    </div>
</template>

<script setup>
defineProps({
    label: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    type: { type: String, default: 'text' },
    name: { type: String, default: '' },
    modelValue: { type: [String, Number], default: '' },
    error: { type: String, default: '' }
});

defineEmits(['update:modelValue']);
</script>
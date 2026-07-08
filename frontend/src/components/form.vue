<template>
    <div class="flex flex-col gap-2">
        <label>{{ label }}</label>
        <input 
            :type="type" 
            :name="name" 
            class="w-full border rounded-sm px-4 py-2" 
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        >
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    name: {
        type: String,
        default: ''
    },
    modelValue: {
        type: [String, Number],
        default: ''
    }
});

defineEmits(['update:modelValue']);
</script>
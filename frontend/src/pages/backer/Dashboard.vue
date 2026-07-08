<script setup>
import Card from '@/components/card.vue'
import { ref } from 'vue'

const dummyData = ref([
    { id: 1, name: 'Produk Organik', price: 15000, stock: 50 },
    { id: 2, name: 'Bibit Tanaman', price: 25000, stock: 10 },
    { id: 3, name: 'Pupuk Kompos', price: 10000, stock: 100 },
    { id: 4, name: 'Peralatan Tani', price: 50000, stock: 5 },
    { id: 5, name: 'Sayuran Segar', price: 30000, stock: 0 },
    { id: 6, name: 'Buah-buahan', price: 45000, stock: 20 },
    { id: 7, name: 'Benih Padi', price: 12000, stock: 200 },
    { id: 8, name: 'Traktor Mini', price: 8000000, stock: 2 },
])
</script>

<template>
    <div class="p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Dashboard</h1>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <Card v-for="item in dummyData" :key="item.id" :data="item" />
        </div>
    </div>
</template>
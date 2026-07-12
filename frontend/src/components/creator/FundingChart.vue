<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
    Chart as ChartJS, Title, Tooltip, Legend,
    LineElement, PointElement, CategoryScale, LinearScale, Filler,
} from 'chart.js'
import dayjs from 'dayjs'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler)

const props = defineProps({
    stats: { type: Array, default: () => [] }, // [{date, amount}]
})

const chartData = computed(() => ({
    labels: props.stats.map(s => dayjs(s.date).format('DD MMM')),
    datasets: [{
        label: 'Dana Terkumpul',
        data: props.stats.map(s => Number(s.amount)),
        borderColor: '#1d4ed8',
        backgroundColor: 'rgba(29,78,216,0.1)',
        tension: 0.3,
        fill: true,
    }]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: {
            ticks: {
                callback: (value) => 'Rp' + Number(value).toLocaleString('id-ID')
            }
        }
    }
}
</script>

<template>
    <div class="h-64">
        <Line v-if="stats.length" :data="chartData" :options="chartOptions" />
        <p v-else class="text-center text-gray-400 py-20">Belum ada data funding untuk kampanye ini.</p>
    </div>
</template>
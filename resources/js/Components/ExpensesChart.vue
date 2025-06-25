<template>
    <div class="chart-container">
        <canvas ref="chart"></canvas>
    </div>
</template>

<script setup>
import { Chart, registerables } from 'chart.js';
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';

Chart.register(...registerables);

const props = defineProps({
    expensesChart: {
        type: Object,
        required: true
    },
    expOptions: {
        type: Object,
        default: () => ({})
    }
});

const chart = ref(null);
let chartInstance = null;

function createChart(){
    if(chartInstance){
        chartInstance.destroy();
    }
    chartInstance = new Chart(chart.value, {
        type: 'bar',
        data: props.expensesChart,
        options: props.expOptions
    });
}

onMounted(() => {
    if(chart.value){
        createChart();
    }
});

onBeforeUnmount(() => {
    if(chartInstance){
        chartInstance.destroy();
    }
});

watch(() => props.expensesChart, (newVal) => {
    if(chartInstance){
        chartInstance.data = newVal;
        chartInstance.update();
    }
}, { deep: true });
</script>

<style scoped>
.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
}
</style>

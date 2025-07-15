<template>
	<div class="chart-container">
		<canvas ref="chart"></canvas>
	</div>
</template>

<script setup>
import { Chart, registerables } from 'chart.js';
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';

Chart.register(...registerables);

const props = defineProps({
	yearlySalesChart: {
		type: Object,
		required: true
	},
	yearlyExpChart: {
		type: Object,
		required: true
	},
	yearlySalesOptions: {
		type: Object,
		default: () => ({})
	},
	yearlyExpOptions: {
		type: Object,
		default: () => ({})
	}
});

const chart = ref(null);
let chartInstance = null;

const createChart = () => {
	if (!chart.value) return;

	//merge datasets from both props
	const datasets = [
		...props.yearlySalesChart.datasets,
		...props.yearlyExpChart.datasets
	];

	const config = {
		type: 'bar',
		data: {
		labels: props.yearlySalesChart.labels || props.yearlyExpChart.labels,
		datasets: datasets
		},
		options: {
		responsive: true,
		plugins: {
			legend: {
			position: 'top',
			},
			title: {
			display: true,
			text: 'Yearly Sales vs Expenses'
			}
		},
		scales: {
			x: { stacked: true },
			y: { stacked: true }
		},
		...props.yearlySalesOptions,
		...props.yearlyExpOptions
		}
	};

	if (chartInstance) chartInstance.destroy();
	chartInstance = new Chart(chart.value, config);
};

onMounted(createChart);
onBeforeUnmount(() => chartInstance?.destroy());

//update chart when props change
watch(() => [
	props.yearlySalesChart,
	props.yearlyExpChart,
	props.yearlySalesOptions,
	props.yearlyExpOptions
], createChart, { deep: true });
</script>

<style scoped>
.chart-container {
	position: relative;
	height: 400px;
	width: 100%;
}
</style>
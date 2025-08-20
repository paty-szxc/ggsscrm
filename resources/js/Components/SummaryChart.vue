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
	salesDataChart: {
		type: Object,
		required: true
	},
	expDataChart: {
		type: Object,
		required: true
	},
	salesDataOptions: {
		type: Object,
		default: () => ({})
	},
	expDataOptions: {
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
		...props.salesDataChart.datasets,
		...props.expDataChart.datasets
	];

	const config = {
		type: 'bar',
		data: {
		labels: props.salesDataChart.labels || props.expDataChart.labels,
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
			text: 'Sales vs Expenses'
			}
		},
		scales: {
			x: { stacked: true },
			y: { stacked: true }
		},
		...props.salesDataOptions,
		...props.expDataOptions
		}
	};

	if (chartInstance) chartInstance.destroy();
	chartInstance = new Chart(chart.value, config);
};

onMounted(createChart);
onBeforeUnmount(() => chartInstance?.destroy());

//update chart when props change
watch(() => [
	props.salesDataChart,
	props.expDataChart,
	props.salesDataOptions,
	props.expDataOptions
], createChart, { deep: true });
</script>

<style scoped>
.chart-container {
	position: relative;
	width: 100%;
	height: 50vw;
	max-height: 400px;
	min-height: 200px;
}
</style>
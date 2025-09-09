<template>
	<div class="chart-container">
		<canvas ref="chart"></canvas>
	</div>
</template>

<script setup>
import { Chart, registerables } from 'chart.js';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

Chart.register(...registerables);

const props = defineProps({
	chartOptions: {
		type: Object,
		default: () => ({})
	},
	salesUrl: {
		type: String,
		default: '/yearly_sales'
	},
	expensesUrl: {
		type: String,
		default: '/yearly_expenses'
	},
	title: {
		type: String,
		default: 'Yearly Sales vs Expenses'
	},
	labelSales: {
		type: String,
		default: 'Sales'
	},
	labelExpenses: {
		type: String,
		default: 'Expenses'
	},
	manualDataOverride: {
		type: Object,
		default: null
	}
});

const chart = ref(null);
let chartInstance = null;
const currentYearData = ref(null);

//manual data for previous years (2021-2024) with default values
const manualData = ref(props.manualDataOverride || {
	2021: { sales_revenue: 3545150, expenses: 800000 },
	2022: { sales_revenue: 7232601, expenses: 950000 },
	2023: { sales_revenue: 16635500, expenses: 1100000 },
	2024: { sales_revenue: 16695850, expenses: 1200000 }
});

const fetchCurrentYearData = async () => {
	try{
		const currentYear = new Date().getFullYear();
		
		//always fetch current year data, regardless of manual data
		let sales = { total_sales: 0 };
		let expenses = { grand_total: 0 };
		
		try{
			const res = await axios.get(props.salesUrl);
			sales = res.data;
		} 
		catch(e){
			console.error('Sales fetch failed:', e);
		}
		
		try{
			const res = await axios.get(props.expensesUrl);
			expenses = res.data;
		}
		catch(e){
			console.error('Expenses fetch failed:', e);
		}

		console.log('Sales data received:', sales);
		console.log('Expenses data received:', expenses);
		
		currentYearData.value = {
			year: currentYear,
			sales_revenue: sales.total_sales || 0,
			expenses: expenses.grand_total || 0,
			is_manual: false
		};
		
		console.log('Current year data set:', currentYearData.value);
		
		createChart();
	}
	catch(error){
		console.error('Unexpected error:', error);
		createChart();
	}
};

const createChart = () => {
	if(!chart.value) return;

	console.log('Creating chart with current year data:', currentYearData.value);

	//combine manual data with current year data
	const allData = [];
	
	//add manual years (2021-2024)
	Object.keys(manualData.value).forEach(year => {
		const data = manualData.value[year];
		allData.push({
		year: parseInt(year),
		sales_revenue: data.sales_revenue || 0,
		expenses: data.expenses || 0,
		is_manual: true
		});
});

//add current year data if available (2025)
if(currentYearData.value){
allData.push(currentYearData.value);
}

//sort by year
allData.sort((a, b) => a.year - b.year);

console.log('Final chart data:', allData);

const years = allData.map(item => item.year);
const salesData = allData.map(item => item.sales_revenue);
const expensesData = allData.map(item => item.expenses);

console.log('Chart labels:', years);
console.log('Sales data:', salesData);
console.log('Expenses data:', expensesData);

const config = {
	type: 'bar',
	data: {
	labels: years,
	datasets: [
		{
		label: props.labelSales,
		data: salesData,
		backgroundColor: 'rgba(54, 162, 235, 0.7)',
		borderColor: 'rgba(54, 162, 235, 1)',
		borderWidth: 1
		},
		{
		label: props.labelExpenses,
		data: expensesData,
		backgroundColor: 'rgba(255, 99, 132, 0.7)',
		borderColor: 'rgba(255, 99, 132, 1)',
		borderWidth: 1
		}
	]
	},
	options: {
	responsive: true,
	maintainAspectRatio: false,
	plugins: {
		legend: {
		position: 'top',
		},
		title: {
		display: true,
		text: props.title,
		font: { size: 16 }
		},
				tooltip: {
			callbacks: {
				label: function(context) {
				let value = context.parsed ? context.parsed.y : context.raw;
				let numericValue = Number(value);
				return isNaN(numericValue) ? 'Invalid value' : `${context.dataset.label}: ₱${numericValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
				}
			}
		}
	},
		scales: {
		x: {
		stacked: true
		},
		y: {
			beginAtZero: true,
			stacked: true,
			ticks: {
				callback: function(value) {
				return `₱${value.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
				}
			}
		}
	},
	...props.chartOptions
	}
};

	if(chartInstance) chartInstance.destroy();
	chartInstance = new Chart(chart.value, config);
};

onMounted(() => {
	fetchCurrentYearData();
});

onBeforeUnmount(() => chartInstance?.destroy());
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
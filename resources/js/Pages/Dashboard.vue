<template>
    <div class="grid grid-cols-2 gap-8 pl-5 pt-2">
        <div>
            <MonthlyChart :barChart="barChart" :barOptions="barOptions"/>
        </div>
        <div>
            <ExpensesChart :expensesChart="expensesChart" :expOptions="expOptions"></ExpensesChart>
        </div>
        <div>
            <SummaryChart 
                :salesDataChart="summarySalesData"
                :expDataChart="summaryExpensesData"
                :salesDataOptions="summaryOptions"
                :expDataOptions="summaryOptions"
            />
        </div>
    </div>
</template>

<script setup>
import SummaryChart from '../Components/SummaryChart.vue'
import MonthlyChart from '../Components/MonthlyChart.vue'
import ExpensesChart from '../Components/ExpensesChart.vue';
import { ref, onMounted } from 'vue'
import axios from 'axios';

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const colors = [
    'rgba(128, 174, 221, 0.7)', 'rgba(219, 112, 147, 0.7)', 'rgba(152, 251, 152, 0.7)',
    'rgba(255, 192, 203, 0.7)', 'rgba(147, 112, 219, 0.7)', 'rgba(64, 224, 208, 0.7)', 
    'rgba(255, 127, 80, 0.7)', 'rgba(255, 215, 0, 0.7)', 'rgba(218, 165, 32, 0.7)', 
    'rgba(210, 105, 30, 0.7)', 'rgba(139, 69, 19, 0.7)', 'rgba(176, 196, 222, 0.7)',
];
const borderColors = [
    'rgba(128, 174, 221, 1', 'rgba(219, 112, 147, 1', 'rgba(152, 251, 152, 1',
    'rgba(255, 192, 203, 1', 'rgba(147, 112, 219, 1', 'rgba(64, 224, 208, 1', 
    'rgba(255, 127, 80, 1', 'rgba(255, 215, 0, 1', 'rgba(218, 165, 32, 1', 
    'rgba(210, 105, 30, 1', 'rgba(139, 69, 19, 1', 'rgba(176, 196, 222, 1',
]

const createChartData = (label, data) => ({
    labels: months,
    datasets: [{
        label,
        data,
        backgroundColor: colors,
        borderColor: borderColors,
        borderWidth: 1
    }]
});

const createChartOptions = (title) => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        title: {
            display: true,
            text: title,
            font: { size: 16 }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let value = context.parsed && typeof context.parsed.y === 'number' ? context.parsed.y : context.raw;
                    let numericValue = Number(value);
                    return isNaN(numericValue) ? 'Invalid value' : `${numericValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    return `${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            }
        }
    }
});

const summarySalesData = ref({
    labels: months,
    datasets: [{
        label: 'Sales',
        data: Array(12).fill(0),
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    }]
});

const summaryExpensesData = ref({
    labels: months,
    datasets: [{
        label: 'Expenses',
        data: Array(12).fill(0),
        backgroundColor: 'rgba(255, 99, 132, 0.7)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
    }]
});

const summaryOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Sales vs Expenses Summary',
            font: { size: 16 }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let value = context.parsed && typeof context.parsed.y === 'number' ? context.parsed.y : context.raw;
                    let numericValue = Number(value);
                    return isNaN(numericValue) ? 'Invalid value' : `${numericValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            stacked: true,
            ticks: {
                callback: function(value) {
                    return `${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            }
        },
        x: {
            stacked: true
        }
    }
};

const expensesChart = ref(createChartData('Expenses', Array(12).fill(0)));
const expOptions = createChartOptions('Monthly Expenses');

const barChart = ref(createChartData('Project Cost', Array(12).fill(0)));
const barOptions = createChartOptions('Monthly Project Sales');

const fetchMonthlyCosts = async () => {
    try{
        const res = await axios.get('/monthly_totals');
        const monthlyData = await res.data;
        barChart.value.datasets[0].data = monthlyData.map(item => item.total_cost);

        summarySalesData.value.datasets[0].data = monthlyData.map(item => item.total_cost);
    } 
    catch(error){
        console.error('Error fetching monthly costs:', error);
    }
}

const fetchMonthlyExpenses = async () => {
    try{
        const res = await axios.get('/monthly_expenses')
        const monthlyExpenses = await res.data
        expensesChart.value.datasets[0].data = monthlyExpenses.map(item => item.grand_total)

        summaryExpensesData.value.datasets[0].data = monthlyExpenses.map(item => item.grand_total);
    }
    catch(error){
        console.error('Error fetching Monthly Expenses', error)
    }
}

onMounted(() => {
    fetchMonthlyCosts();
    fetchMonthlyExpenses();
});

</script>
<template>
    <h1 class="p-4 font-sans indent-8 text-2xl">Sales & Revenue</h1>

    <SalesRevenue
        :headers="headers"
        :sales_revenue_data="salesRevenueData"
        @refresh-data="fetchSalesRevenueData">
    </SalesRevenue>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import SalesRevenue from '../Components/SalesRevenueTable.vue';

const headers = ref([
    { title: 'Date of Survey', value: 'date_of_survey', align: 'center' },
    { title: 'Location', value: 'location', align: 'center' },
    { title: 'Type of Survey', value: 'type_of_survey', align: 'center' },
    { title: 'Expenses', value: 'expenses', align: 'center' },
    { title: 'Receipt No.', value: 'receipt_no', align: 'center' },
    { title: 'Project Cost', value: 'project_cost', align: 'center' },
    { 
        title: 'Date of Collections', 
        align: 'center', 
        children: [
            { 
                title: '1st', 
                align: 'center',
                children: [
                    { title: 'Date', value: 'first_date_of_collection', align: 'center' },
                    { title: 'Amount', value: 'first_collection', align: 'center' }
                ]
            },
            { 
                title: '2nd', 
                align: 'center',
                children: [
                    { title: 'Date', value: 'second_date_of_collection', align: 'center' },
                    { title: 'Amount', value: 'second_collection', align: 'center' }
                ]
            },
            { 
                title: '3rd', 
                align: 'center',
                children: [
                    { title: 'Date', value: 'third_date_of_collection', align: 'center' },
                    { title: 'Amount', value: 'third_collection', align: 'center' }
                ]
            },
            { 
                title: '4th', 
                align: 'center',
                children: [
                    { title: 'Date', value: 'fourth_date_of_collection', align: 'center' },
                    { title: 'Amount', value: 'fourth_collection', align: 'center' }
                ]
            },
        ]
    },
    { title: 'Total', value: 'total', align: 'center' }
])

const salesRevenueData = ref([])

const fetchSalesRevenueData = async () => {
    try{
        const res = await axios.get('/get_sales_revenue_data')
        salesRevenueData.value = res.data
        console.log(res.data)
    }
    catch(error){
        console.error('Error fetching sales and revenue data', error)
    }
}

onMounted(() => {
    fetchSalesRevenueData()
})
</script>
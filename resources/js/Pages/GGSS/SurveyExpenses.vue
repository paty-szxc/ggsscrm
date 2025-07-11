<template>
    <div>
        <ExpensesTable
            @refresh-data="fetchVouchers"
            :voucher_headers="voucherHeaders"
            :vouchers="vouchersData"
        />
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import ExpensesTable from '../../Components/ExpensesTable.vue';

const voucherHeaders = ref([
    { title: 'Voucher No.', value: 'voucher_no', align: 'center' },
    { title: 'Date', value: 'date', align: 'center', class: 'text-sm' },
    { title: 'Supplier Name', value: 'suppliers_name', align: 'center' },
    { title: 'Address (Brgy & City)', value: 'address_brgy_city', align: 'center' },
    { title: 'VAT Status', value: 'status_of_vat', align: 'center' },
    { title: 'TIN', value: 'tin', align: 'center' },
    { title: 'Particulars', value: 'particulars', align: 'center' },
    { title: 'Employee - Salary', value: 'employee_salary', align: 'center' },
    { title: 'Employee - Benefits', value: 'employee_benefits', align: 'center' },
    { title: 'Meals (Office/Survey)', value: 'meals_office_survey', align: 'center' },
    { title: 'Dog Food', value: 'dog_food', align: 'center' },
    { title: 'Construction & Survey Supplies', value: 'construction_survey_supplies', align: 'center' },
    { title: 'Repair & Maintenance', value: 'repairs_maintenance', align: 'center' },
])
const vouchersData = ref([])

// const benefitsHeaders = ref([
//     { title: 'Benefit Type', value: 'type', align: 'center' },
//     { title: 'Amount', value: 'amount', align: 'center' },
//     { title: 'Date', value: 'date', align: 'center' }
// ])
// const employeeBenefits = ref([])

// const repairHeaders = ref([
//     { title: 'Item', value: 'item', align: 'center' },
//     { title: 'Cost', value: 'cost', align: 'center' },
//     { title: 'Date', value: 'date', align: 'center' }
// ])
// const repairsData = ref([])

const fetchVouchers = async () => {
    try{
        const res = await axios.get('/get_vouchers')
        vouchersData.value = res.data
        console.log(res.data)
    }
    catch(error){
        console.error('Error fetching data', error)
    }
}

onMounted(() => {
    fetchVouchers()
})
</script>
<template>
    <div>
        <ExpensesTable
            :voucher_headers="voucherHeaders"
            :vouchers="vouchersData"
            @refresh-data="fetchVouchers"
            @open-dialog="openAddDialog"
            @edit-data="openEditDialog"
        />

        <v-dialog class="transition-discrete md:transition-normal" v-model="dialog" no-click-animation persistent width="700">
            <v-card class="w-full">
            <v-card-title 
                style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;">
                {{ isEditMode ? 'Edit' : 'Add' }}
            </v-card-title>
            <v-card-text>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 item-start">
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Voucher No."
                        v-model="tempData.voucher_no"
                        >
                    </v-text-field>
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.date"
                    />
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Suppliers Name"
                        v-model="tempData.suppliers_name">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Address (Brgy. & City)"
                        v-model="tempData.address_brgy_city">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Status of Vat"
                        v-model="tempData.status_of_vat">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Tin"
                        v-model="tempData.tin">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Particulars"
                        v-model="tempData.particulars">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Employee Salary"
                        v-model="tempData.employee_salary">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Employee Benefits"
                        v-model="tempData.employee_benefits">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Meals (Office/Survey)"
                        v-model="tempData.meals_office_survey">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Dog Food"
                        v-model="tempData.dog_food">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Construction & Survey Supplies"
                        v-model="tempData.construction_survey_supplies">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Repairs & Maintenance"
                        v-model="tempData.repairs_maintenance">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Office Supplies"
                        v-model="tempData.office_supplies">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Gasoline & Oil"
                        v-model="tempData.gasoline_oil">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Utilities (Electric, Water, Internet, Assoc Dues)"
                        v-model="tempData.utilities">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Parking Fee"
                        v-model="tempData.parking_fee">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Toll Fee"
                        v-model="tempData.toll_fee">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Permits, Certification & Tax "
                        v-model="tempData.permits_certification_tax">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Transportation/Shipping"
                        v-model="tempData.transportation">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Budget (Survey/Outside Office)/Commission or SOP"
                        v-model="tempData.budget">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Representation Expense (Personal - Sir Pete)"
                        v-model="tempData.representation_expense_personal">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="OTHERS (Staff - Personal)"
                        v-model="tempData.others_staff_personal">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="AMOUNT ( GROSS OF VAT)"
                        v-model="tempData.amount_gross_of_vat">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="NET OF VAT"
                        v-model="tempData.net_of_vat">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="VAT"
                        v-model="tempData.vat">
                    </v-text-field>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn 
                    @click="closeAddDialog"
                    color="red darken-1"
                    text>
                    Cancel
                </v-btn>
                <v-btn
                    @click="submitForm"
                    color="green darken-1"
                    text>
                    Submit
                </v-btn>
            </v-card-actions>
        </v-card>
        </v-dialog>

        <Snackbar ref="snackbar"></Snackbar>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import ExpensesTable from '../../Components/ExpensesTable.vue';
import Snackbar from '../../Components/Snackbar.vue';
import { isTemplateNode } from '@vue/compiler-core';

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
const tempData = ref({})
const dialog = ref(false)
const isEditMode = ref(false)
const snackbar = ref(null)

const submitForm = async () =>  {
    const to_update = { ...tempData.value }

    const parseCurrencyValue = (value) => {
        if(value === null || value === undefined || value === '') return null
        if(typeof value === 'number') return value
        return parseFloat(value.replace(/,/g, ''))
    }

    //parse all currency fields
    to_update.employee_salary = parseCurrencyValue(to_update.employee_salary)
    to_update.employee_benefits = parseCurrencyValue(to_update.employee_benefits)
    to_update.meals_office_survey = parseCurrencyValue(to_update.meals_office_survey)
    to_update.dog_food = parseCurrencyValue(to_update.dog_food)
    to_update.construction_survey_supplies = parseCurrencyValue(to_update.construction_survey_supplies)
    to_update.repairs_maintenance = parseCurrencyValue(to_update.repairs_maintenance)
    to_update.office_supplies = parseCurrencyValue(to_update.office_supplies)
    to_update.gasoline_oil = parseCurrencyValue(to_update.gasoline_oil)
    to_update.utilities = parseCurrencyValue(to_update.utilities)
    to_update.parking_fee = parseCurrencyValue(to_update.parking_fee)
    to_update.toll_fee = parseCurrencyValue(to_update.toll_fee)
    to_update.permits_certification_tax = parseCurrencyValue(to_update.permits_certification_tax)
    to_update.transportation = parseCurrencyValue(to_update.transportation)
    to_update.budget = parseCurrencyValue(to_update.budget)
    to_update.representation_expense_personal = parseCurrencyValue(to_update.representation_expense_personal)
    to_update.others_staff_personal = parseCurrencyValue(to_update.others_staff_personal)
    to_update.amount_gross_of_vat = parseCurrencyValue(to_update.amount_gross_of_vat)
    to_update.vat = parseCurrencyValue(to_update.vat)

    try{
        if(isEditMode.value){
            await axios({
                method: 'post',
                url: 'update_vouchers_data',
                data: { to_update }
            })
            snackbar.value.alertUpdate()
        }
        else{
            await axios({
                method: 'post',
                url: 'insert_vouchers_data',
                data: { to_update }
            })
            snackbar.value.alertSuccess()
        }
        fetchVouchers()
        dialog.value = false
        if(!isEditMode.value){
            tempData.value = {}
        }
    }
    catch(err){
        console,error('Error saving voucher expenses', err)
        snackbar.value.alertError()
    }
}

const openEditDialog = (item) => {
    tempData.value = { ...item }
    isEditMode.value = true
    dialog.value = true
}

const closeAddDialog = () => {
    dialog.value = false
    tempData.value = {}
    fetchVouchers()
}

const openAddDialog = () => {
    isEditMode.value = false
    dialog.value = true
}

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
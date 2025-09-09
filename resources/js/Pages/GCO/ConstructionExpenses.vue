<template>
    <div>
        <!-- date range filter -->
        <div class="p-4 bg-white rounded-lg mb-4">
            <h3 class="text-lg font-semibold mb-3">Weekly Expenses Filter</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <v-date-input
                    clearable
                    density="compact"
                    hide-details
                    label="Start Date"
                    prepend-icon=""
                    prepend-inner-icon="mdi-calendar"
                    variant="outlined"
                    v-model="dateRange.startDate"
                    @update:model-value="filterByDateRange"
                />
                <v-date-input
                    clearable
                    density="compact"
                    hide-details
                    label="End Date"
                    prepend-icon=""
                    prepend-inner-icon="mdi-calendar"
                    variant="outlined"
                    v-model="dateRange.endDate"
                    @update:model-value="filterByDateRange"
                />
                <div class="flex items-center">
                    <v-btn
                        @click="clearDateFilter"
                        color="secondary"
                        variant="flat"
                        class="mr-2">
                        Clear Filter
                    </v-btn>
                    <v-btn
                        @click="exportWeeklyReport"
                        color="primary"
                        variant="flat">
                        Export Report
                    </v-btn>
                </div>
            </div>
            
            <div v-if="weeklyTotals" class="mt-4 p-4 bg-blue-50 rounded-lg">
                <h4 class="text-md font-semibold mb-2">Weekly Summary ({{ formatDate(dateRange.startDate) }} - {{ formatDate(dateRange.endDate) }})</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <!-- column 1 -->
                    <div class="bg-white p-3 rounded border shadow-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Meals</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.meals_office_survey) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Construction Supplies</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.construction_survey_supplies) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Repairs Maintenance</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.repairs_maintenance) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Office Supplies</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.office_supplies) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Gasoline & Oil</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.gasoline_oil) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- column 2 -->
                    <div class="bg-white p-3 rounded border shadow-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Utilities</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.utilities) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Parking Fee</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.parking_fee) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Toll Fee</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.toll_fee) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Permits, Certification & Tax </span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.permits_certification_tax) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Transportation/Shipping</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.transportation) }}</span>
                            </div>

                        </div>
                    </div>
                    
                    <!-- column 3 -->
                    <div class="bg-white p-3 rounded border shadow-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Budget(Survey/Outside Office)/Commission or SOP</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.budget) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Representation Expense(Personal - Sir Pete)</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.representation_expense_personal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">OTHERS(Staff - Personal)</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.others_staff_personal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">AMOUNT( GROSS OF VAT)</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.amount_gross_of_vat) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">VAT</span>
                                <span class="font-bold text-green-600">₱{{ formatCurrency(weeklyTotals.vat) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- column 4 -->
                    <div class="bg-white p-3 rounded border shadow-sm flex flex-col justify-center items-center">
                        <div class="text-center">
                            <div class="font-medium text-gray-600 mb-2">Total Expenses</div>
                            <div class="text-2xl font-bold text-blue-600">₱{{ formatCurrency(weeklyTotals.total) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ExpensesTable
            :voucher_headers="voucherHeaders"
            :vouchers="filteredVouchersData"
            expensesType="Construction"
            importUrl="/import_construction_vouchers_data"
            :expandedColumns="expandedColumns"
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
                    <!-- <v-text-field
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
                    </v-text-field> -->
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Meals (Office/Survey)"
                        v-model="tempData.meals_office_survey">
                    </v-text-field>
                    <!-- <v-text-field
                        class="mt-3"
                        hide-details
                        label="Dog Food"
                        v-model="tempData.dog_food">
                    </v-text-field> -->
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

const voucherHeaders = ref([
    { title: 'Voucher No.', value: 'voucher_no', align: 'center' },
    { title: 'Date', value: 'date', align: 'center', class: 'text-sm' },
    { title: 'Supplier Name', value: 'suppliers_name', align: 'center' },
    { title: 'Address (Brgy & City)', value: 'address_brgy_city', align: 'center' },
    { title: 'VAT Status', value: 'status_of_vat', align: 'center' },
    { title: 'TIN', value: 'tin', align: 'center' },
    { title: 'Particulars', value: 'particulars', align: 'center' },
    //{ title: 'Employee - Salary', value: 'employee_salary', align: 'center' },
    // { title: 'Employee - Benefits', value: 'employee_benefits', align: 'center' },
    { title: 'Meals (Office/Survey)', value: 'meals_office_survey', align: 'center' },
    // { title: 'Dog Food', value: 'dog_food', align: 'center' },
    { title: 'Construction & Survey Supplies', value: 'construction_survey_supplies', align: 'center' },
    { title: 'Repair & Maintenance', value: 'repairs_maintenance', align: 'center' },
    { title: 'Office Supplies', value: 'office_supplies', align: 'center' },
    { title: 'Gasoline & Oil', value: 'gasoline_oil', align: 'center' },
])
const vouchersData = ref([])
const tempData = ref({})
const dialog = ref(false)
const isEditMode = ref(false)
const snackbar = ref(null)

//expanded columns for construction vouchers expanded row
const expandedColumns = ref([
    { title: 'Utilities (Electricty, Water, Internet, Assoc Dues)', key: 'utilities' },
    { title: 'Parking Fee', key: 'parking_fee' },
    { title: 'Toll Fee', key: 'toll_fee' },
    { title: 'Permits, Certification, & Tax', key: 'permits_certification_tax' },
    { title: 'Transportation', key: 'transportation' },
    { title: 'Budget (Survey/Outside Office)/Commission or SOP', key: 'budget' },
    { title: 'Representation Expense (Personal - Sir Pete)', key: 'representation_expense_personal' },
    { title: 'OTHERS (Staff - Personal)', key: 'others_staff_personal' },
    { title: 'AMOUNT (GROSS OF VAT)', key: 'amount_gross_of_vat' },
    { title: 'NET VAT', key: 'net_of_vat' },
    { title: 'VAT', key: 'vat' },
])

//date range filtering
const dateRange = ref({
    startDate: null,
    endDate: null
})
const filteredVouchersData = ref([])
const weeklyTotals = ref(null)

const submitForm = async () =>  {
    const to_update = { ...tempData.value }

    const parseCurrencyValue = (value) => {
        if(value === null || value === undefined || value === '') return null
        if(typeof value === 'number') return value
        return parseFloat(value.replace(/,/g, ''))
    }

    //parse all currency fields
    // to_update.employee_salary = parseCurrencyValue(to_update.employee_salary)
    // to_update.employee_benefits = parseCurrencyValue(to_update.employee_benefits)
    to_update.meals_office_survey = parseCurrencyValue(to_update.meals_office_survey)
    // to_update.dog_food = parseCurrencyValue(to_update.dog_food)
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
                url: 'update_construction_vouchers_data',
                data: { to_update }
            })
            snackbar.value.alertUpdate()
        }
        else{
            await axios({
                method: 'post',
                url: 'insert_construction_vouchers_data',
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
        const res = await axios.get('/get_construction_vouchers')
        vouchersData.value = res.data
        filteredVouchersData.value = res.data //initialize filtered data
        console.log(res.data)
    }
    catch(error){
        console.error('Error fetching data', error)
    }
}

// Date range filtering functions
const filterByDateRange = () => {
    if(!dateRange.value.startDate || !dateRange.value.endDate){
        filteredVouchersData.value = vouchersData.value
        weeklyTotals.value = null
        return
    }

    const startDate = new Date(dateRange.value.startDate)
    const endDate = new Date(dateRange.value.endDate)
    
    //set time to start and end of day
    startDate.setHours(0, 0, 0, 0)
    endDate.setHours(23, 59, 59, 999)

    filteredVouchersData.value = vouchersData.value.filter(voucher => {
        const voucherDate = new Date(voucher.date)
        return voucherDate >= startDate && voucherDate <= endDate
    })

    calculateWeeklyTotals()
}

const calculateWeeklyTotals = () => {
    if (filteredVouchersData.value.length === 0) {
        weeklyTotals.value = null
        return
    }

    const totals = {
        // employee_salary: 0,
        // employee_benefits: 0,
        meals_office_survey: 0,
        // dog_food: 0,
        construction_survey_supplies: 0,
        repairs_maintenance: 0,
        office_supplies: 0,
        gasoline_oil: 0,
        utilities: 0,
        parking_fee: 0,
        toll_fee: 0,
        permits_certification_tax: 0,
        transportation: 0,
        budget: 0,
        representation_expense_personal: 0,
        others_staff_personal: 0,
        amount_gross_of_vat: 0,
        vat: 0,
        total: 0
    }

    filteredVouchersData.value.forEach(voucher => {
        // Parse numeric values, defaulting to 0 if null/undefined
        const parseValue = (value) => {
            if(value === null || value === undefined || value === '') return 0
            return parseFloat(value.toString().replace(/,/g, '')) || 0
        }

        // totals.employee_salary += parseValue(voucher.employee_salary)
        // totals.employee_benefits += parseValue(voucher.employee_benefits)
        totals.meals_office_survey += parseValue(voucher.meals_office_survey)
        // totals.dog_food += parseValue(voucher.dog_food)
        totals.construction_survey_supplies += parseValue(voucher.construction_survey_supplies)
        totals.repairs_maintenance += parseValue(voucher.repairs_maintenance)
        totals.office_supplies += parseValue(voucher.office_supplies)
        totals.gasoline_oil += parseValue(voucher.gasoline_oil)
        totals.utilities += parseValue(voucher.utilities)
        totals.parking_fee += parseValue(voucher.parking_fee)
        totals.toll_fee += parseValue(voucher.toll_fee)
        totals.permits_certification_tax += parseValue(voucher.permits_certification_tax)
        totals.transportation += parseValue(voucher.transportation)
        totals.budget += parseValue(voucher.budget)
        totals.representation_expense_personal += parseValue(voucher.representation_expense_personal)
        totals.others_staff_personal += parseValue(voucher.others_staff_personal)
        totals.amount_gross_of_vat += parseValue(voucher.amount_gross_of_vat)
        totals.vat += parseValue(voucher.vat)
    })

    //calculate total
    totals.total = Object.keys(totals).reduce((sum, key) => {
        if(key !== 'total'){
            return sum + totals[key]
        }
        return sum
    }, 0)

    weeklyTotals.value = totals
}

const clearDateFilter = () => {
    dateRange.value.startDate = null
    dateRange.value.endDate = null
    filteredVouchersData.value = vouchersData.value
    weeklyTotals.value = null
}

const formatDate = (date) => {
    if(!date) return ''
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatCurrency = (amount) => {
    if(amount === null || amount === undefined) return '0.00'
    return parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}

const exportWeeklyReport = () => {
    if(!weeklyTotals.value) {
        alert('Please select a date range first')
        return
    }

    //create CSV content
    const headers = [
        'Category',
        'Amount'
    ]

    const data = [
        // ['Employee Salary', weeklyTotals.value.employee_salary],
        // ['Employee Benefits', weeklyTotals.value.employee_benefits],
        ['Meals (Office/Survey)', weeklyTotals.value.meals_office_survey],
        // ['Dog Food', weeklyTotals.value.dog_food],
        ['Construction & Survey Supplies', weeklyTotals.value.construction_survey_supplies],
        ['Repairs & Maintenance', weeklyTotals.value.repairs_maintenance],
        ['Office Supplies', weeklyTotals.value.office_supplies],
        ['Gasoline & Oil', weeklyTotals.value.gasoline_oil],
        ['Utilities', weeklyTotals.value.utilities],
        ['Parking Fee', weeklyTotals.value.parking_fee],
        ['Toll Fee', weeklyTotals.value.toll_fee],
        ['Permits Certification & Tax', weeklyTotals.value.permits_certification_tax],
        ['Transportation/Shipping', weeklyTotals.value.transportation],
        ['Budget', weeklyTotals.value.budget],
        ['Representation Expense', weeklyTotals.value.representation_expense_personal],
        ['Others (Staff - Personal)', weeklyTotals.value.others_staff_personal],
        ['Amount (Gross of VAT)', weeklyTotals.value.amount_gross_of_vat],
        ['VAT', weeklyTotals.value.vat],
        ['', ''],
        ['TOTAL EXPENSES', weeklyTotals.value.total]
    ]

    const csvContent = [
        headers.join(','),
        ...data.map(row => row.join(','))
    ].join('\n')

    //create and download file
    const blob = new Blob([csvContent], { type: 'text/csv' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `Weekly Expenses ${formatDate(dateRange.value.startDate)}-${formatDate(dateRange.value.endDate)}.csv`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)
}

onMounted(() => {
    fetchVouchers()
})
</script>
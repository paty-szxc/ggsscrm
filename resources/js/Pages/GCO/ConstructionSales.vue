<template>
    <h1 class="p-4 font-sans indent-8 text-2xl">Sales & Revenue</h1>

    <ConstructionSalesRevenueTable
        :headers="headers"
        :construction_sales_revenue="salesRevenueData"
        @refresh-data="fetchConstructionSalesRevenue"
        @open-dialog="openAddDialog"
        @edit-data="openEditDialog">
    </ConstructionSalesRevenueTable>

    <v-dialog no-click-animation persistent v-model="dialog" width="700px">
        <v-card class="w-full">
            <v-card-title style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;">
                {{ isEditMode ? 'Edit' : 'Add' }}
            </v-card-title>
            <v-card-text>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 item-start">
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        style="width: 100%"
                        v-model="tempData.date"
                    />
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Client Name & Address"
                        v-model="tempData.client_name_address">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Particulars"
                        v-model="tempData.particulars"
                        clearable
                    />
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Status of VAT"
                        v-model="tempData.status_of_vat">
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Receipt No."
                        v-model="tempData.receipt_no">
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Project Cost"
                        v-model="tempData.project_cost"
                        @input="handleCurrencyInput('project_cost')"
                        @blur="formatCurrencyField('project_cost')">
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Amount (Gross of VAT)"
                        v-model="tempData.amount_gross_of_vat"
                        @input="handleCurrencyInput('amount_gross_of_vat')"
                        @blur="formatCurrencyField('amount_gross_of_vat')">
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Net of VAT"
                        v-model="tempData.net_of_vat"
                        @input="handleCurrencyInput('net_of_vat')"
                        @blur="formatCurrencyField('net_of_vat')">
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="VAT"
                        v-model="tempData.vat"
                        @input="handleCurrencyInput('vat')"
                        @blur="formatCurrencyField('vat')">
                    </v-text-field>
                    <v-date-input 
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="1st Collection Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        style="width: 100%"
                        v-model="tempData.first_date_of_collection">
                    </v-date-input>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="1st Collection Amount"
                        v-model="tempData.first_collection"
                        @input="handleCurrencyInput('first_collection')"
                        @blur="formatCurrencyField('first_collection')">
                    </v-text-field>
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="2nd Collection Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        style="width: 100%"
                        v-model="tempData.second_date_of_collection">
                    </v-date-input>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="2nd Collection Amount"
                        v-model="tempData.second_collection"
                        @input="handleCurrencyInput('second_collection')"
                        @blur="formatCurrencyField('second_collection')">
                    </v-text-field>
                    <v-date-input 
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="3rd Collection Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        style="width: 100%"
                        v-model="tempData.third_date_of_collection">
                    </v-date-input>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="3rd Collection Amount"
                        v-model="tempData.third_collection"
                        @input="handleCurrencyInput('third_collection')"
                        @blur="formatCurrencyField('third_collection')">
                    </v-text-field>
                    <v-date-input 
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="4th Collection Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        style="width: 100%"
                        v-model="tempData.fourth_date_of_collection">
                    </v-date-input>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="4th Collection Amount"
                        v-model="tempData.fourth_collection"
                        @input="handleCurrencyInput('fourth_collection')"
                        @blur="formatCurrencyField('fourth_collection')">
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Total"
                        :model-value="formattedTotal"
                        readonly>
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Receivable Balance"
                        :model-value="formattedReceivableBal"
                        readonly>
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Others (Subcon)"
                        v-model="tempData.others">
                    </v-text-field>
                    <v-text-field 
                        class="mt-3"
                        hide-details
                        label="Remarks"
                        v-model="tempData.remarks">
                    </v-text-field>
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Fully Paid Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.fully_paid_date"
                    />
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer/>
                <v-btn 
                    @click="closeAddDialog"
                    color="red darken-1"
                    text>
                    Cancel
                </v-btn>
                <v-btn
                    color="green darken-1"
                    text
                    @click="handleSubmit">
                    Submit
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <Snackbar ref="snackbar"></Snackbar>
</template>

<script setup>
import { onMounted, ref, watch, nextTick, computed } from 'vue';
import axios from 'axios';
import ConstructionSalesRevenueTable from '../../Components/ConstructionSalesRevenueTable.vue';
import Snackbar from '../../Components/Snackbar.vue';

const headers = ref([
    { title: 'Date', value: 'date', align: 'center' },
    { title: 'Client Name & Address', value: 'client_name_address', align: 'center' },
    { title: 'Particulars', value: 'particulars', align: 'center' },
    { title: 'Status of Vat', value: 'status_of_vat', align: 'center' },
    { title: 'Receipt No.', value: 'receipt_no', align: 'center' },
    { title: 'Project Cost', value: 'project_cost', align: 'center' },
    { title: 'Amount (Gross of VAT)', value: 'amount_gross_of_vat', align: 'center' },
    { title: 'Net of VAT', value: 'net_of_vat', align: 'center' },
    { title: 'VAT', value: 'vat', align: 'center' },
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
])

const salesRevenueData = ref([])
const tempData = ref({})
const dialog = ref(false)
const isEditMode = ref(false)
const snackbar = ref(null)

const formattedTotal = computed(() => formatCurrency(tempData.value.total))
const formattedReceivableBal = computed(() => {
    return !tempData.value.receivable_bal ? '' : formatCurrency(tempData.value.receivable_bal);
})

const getWithholdingTaxAmount = (withholdingTax, projectCost) => {
    if(!withholdingTax) return 0;
    if(!isNaN(withholdingTax)) return parseFloat(withholdingTax);
    const match = /([\d.]+)\s*%/.exec(withholdingTax);
    if(match){
        const percent = parseFloat(match[1]);
        return projectCost * (percent / 100);
    }
    return 0;
};

const calculateTotals = () => {
    const parseAmount = (val) => {
        if(val === null || val === undefined || val === '') return 0
        if(typeof val === 'number') return val
        //remove commas and parse
        return parseFloat(val.toString().replace(/,/g, '')) || 0
    }

    const first = parseAmount(tempData.value.first_collection)
    const second = parseAmount(tempData.value.second_collection)
    const third = parseAmount(tempData.value.third_collection)
    const fourth = parseAmount(tempData.value.fourth_collection)
    const total = first + second + third + fourth
    const projectCost = parseAmount(tempData.value.project_cost)
    const withholdingTax = tempData.value.withholding_tax
    const withholdingTaxAmount = getWithholdingTaxAmount(withholdingTax, projectCost)
    const receivableBal = projectCost - (total + withholdingTaxAmount)

    tempData.value.total = total 
    tempData.value.receivable_bal = receivableBal

    // Set fully paid date if receivable is zero and not already set
    if (receivableBal <= 0 && !tempData.value.fully_paid_date) {
        const dates = [
            tempData.value.first_date_of_collection,
            tempData.value.second_date_of_collection,
            tempData.value.third_date_of_collection,
            tempData.value.fourth_date_of_collection
        ].filter(Boolean).sort()
        tempData.value.fully_paid_date = dates[dates.length - 1] || null
    }
}

const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '0.00'
    
    //remove existing commas if present, then parse
    const num = typeof value === 'string' 
        ? parseFloat(value.replace(/,/g, '')) 
        : Number(value)
    
    return isNaN(num) 
        ? '0.00' 
        : num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
}

const surveyTypes = [
    'Relocation Survey',
    'Subdivision Survey',
    'Topographic Survey',
    'Hydrographic Survey',
    'Verification Survey',
    'Construction Survey',
    'Lot Segregation Survey',
    'Height Clearance Permit',
    'Survey And Lot Plan Approval',
    'As-Built Survey',
    'Re-Survey',
    'Consolidation Survey',
    'Bathymetric',
    'CAD Plotting',
    'Titling Assistance',
    'Land Consultancy',
    'Right Of-Way Survey',
    'Retainership'
]

//when opening add dialog, ensure type_of_survey is an array
const openAddDialog = () => {
    tempData.value = {
        date_of_survey: null,
        location: '',
        type_of_survey: [],
        receipt_no: '',
        project_cost: '',
        first_date_of_collection: '',
        first_collection: '',
        second_date_of_collection: '',
        second_collection: '',
        third_date_of_collection: '',
        third_collection: '',
        fourth_date_of_collection: '',
        fourth_collection: '',
        total: '',
        receivable_bal: '',
        withholding_tax: '',
        remarks: '',
        fully_paid_date: null
    }
    isEditMode.value = false
    dialog.value = true
}

// When editing, convert type_of_survey string to array
const openEditDialog = (item) => {
    const formattedItem = { ...item }
    const currencyFields = ['project_cost', 'first_collection', 'second_collection', 'third_collection', 'fourth_collection', 'total', 'receivable_bal', 'withholding_tax']
    
    currencyFields.forEach(field => {
        if (formattedItem[field] !== null && formattedItem[field] !== undefined && formattedItem[field] !== '') {
            //parse the formatted string back to a number
            const num = parseFloat(formattedItem[field].replace(/,/g, ''))
            if (!isNaN(num)) {
                //store the raw number, let the input handlers format it for display
                formattedItem[field] = num.toString()
            }
        }
    })

    if (formattedItem.type_of_survey && typeof formattedItem.type_of_survey === 'string') {
        formattedItem.type_of_survey = formattedItem.type_of_survey.split('&').map(s => s.trim())
    } else if (!formattedItem.type_of_survey) {
        formattedItem.type_of_survey = []
    }
    
    tempData.value = formattedItem
    isEditMode.value = true
    dialog.value = true
    
    //calculate totals immediately when dialog opens
    nextTick(() => {
        calculateTotals()
    })
};

const handleSubmit = async () => {
    const to_update = { ...tempData.value }

    //function to parse currency values
    const parseCurrencyValue = (value) => {
        if(value === null || value === undefined || value === '') return null
        if(typeof value === 'number') return value
        return parseFloat(value.replace(/,/g, ''))
    }

    //parse all currency fields
    to_update.project_cost = parseCurrencyValue(to_update.project_cost)
    to_update.first_collection = parseCurrencyValue(to_update.first_collection)
    to_update.second_collection = parseCurrencyValue(to_update.second_collection)
    to_update.third_collection = parseCurrencyValue(to_update.third_collection)
    to_update.fourth_collection = parseCurrencyValue(to_update.fourth_collection)
    to_update.total = parseCurrencyValue(to_update.total)
    to_update.receivable_bal = parseCurrencyValue(to_update.receivable_bal)

    //set empty collection amounts to null
    if(to_update.first_collection === '') to_update.first_collection = null
    if(to_update.second_collection === '') to_update.second_collection = null
    if(to_update.third_collection === '') to_update.third_collection = null
    if(to_update.fourth_collection === '') to_update.fourth_collection = null
    
    if(Array.isArray(to_update.type_of_survey)){
        to_update.type_of_survey = to_update.type_of_survey.join(', ')
    }
    
    try{
        if(isEditMode.value){
            await axios({
                method: 'post',
                url: 'update_construction_sales_revenue',
                data: { to_update }
            })
            snackbar.value.alertUpdate()
        }
        else{
            await axios({
                method: 'post',
                url: 'insert_construction_sales_revenue', 
                data: { to_update }
            })
            snackbar.value.alertSuccess()
        }
        fetchConstructionSalesRevenue()
        dialog.value = false
        if(!isEditMode.value){
            tempData.value = {}
        }
    } 
    catch(error){
        console.error('Error saving sales revenue data:', error)
        alert('Error occurred while saving data')
    }
}

const closeAddDialog = () => {
    tempData.value = {}
    dialog.value = false
    fetchConstructionSalesRevenue()
}

const fetchConstructionSalesRevenue = async () => {
    try{
        const res = await axios.get('/get_construction_sales_revenue')
        salesRevenueData.value = res.data
        console.log(res.data)
    }
    catch(error){
        console.error('Error fetching sales and revenue data', error)
    }
}

//calculate totals when dialog opens
watch(dialog, (newVal) => {
    if (newVal) {
        nextTick(() => {
            calculateTotals()
        })
    }
})

watch(() => [
        tempData.value.first_collection,
        tempData.value.second_collection,
        tempData.value.third_collection,
        tempData.value.fourth_collection,
        tempData.value.project_cost,
        tempData.value.withholding_tax
    ],
    () => {
        calculateTotals()
    },
    { deep: true }
)

const handleCurrencyInput = (field) => {
    //remove all non-numeric characters except decimal point
    let value = tempData.value[field]
    if (typeof value === 'string') {
        //remove commas and other non-numeric characters except decimal
        value = value.replace(/[^\d.]/g, '')
        
        //ensure only one decimal point
        const parts = value.split('.')
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('')
        }
        
        tempData.value[field] = value
    }
    
    //calculate totals after input
    calculateTotals()
}

const formatCurrencyField = (field) => {
    let value = tempData.value[field]
    if (value && value !== '') {
        //parse the numeric value
        const num = parseFloat(value)
        if (!isNaN(num)) {
            //format with commas for display
            tempData.value[field] = num.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            })
        }
    }
};

onMounted(() => {
    fetchConstructionSalesRevenue()
})
</script>
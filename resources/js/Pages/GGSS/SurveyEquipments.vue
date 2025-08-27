<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
            <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">Survey Equipments List(s)</h1>
            <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="search">
                    </v-text-field>
                </div>
                <div class="flex-shrink-0">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                @click="openDialog('add')"
                                color="info"
                                icon
                                size="small"
                                v-bind="props">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Add</span>
                    </v-tooltip>
                </div>
            </div>
        </div>
        <v-data-table
            class="font-sans"
            density="compact"
            fixed-footer
            fixed-header
            item-key="id"
            :headers="headers"
            :items="items"
            :search="search">
            <template v-slot:item.status="{ item }">
                <v-chip
                    :color="getStatusColor(item.status)"
                    size="small"
                    variant="flat">
                    {{ item.status }}
                </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-icon
                            @click="openDialog('edit', item)"
                            size="small"
                            class="me-2"
                            color="info"
                            style="cursor: pointer;"
                            v-bind="props">
                            mdi-pencil
                        </v-icon>
                    </template>
                    <span>Edit</span>
                </v-tooltip>
            </template>
        </v-data-table>

        <v-dialog class="transition-discrete md:transition-normal" v-model="dialog" width="450" persistent no-click-animation>
            <v-card>
                <v-card-title style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;"> {{ isEditMode ? 'Edit' : 'Add' }}
                </v-card-title>
                <v-card-text>
                    <v-text-field
                        hide-details
                        label="Description"
                        v-model="tempData.description">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Price"
                        prefix="₱"
                        @input="handleCurrencyInput"
                        @blur="formatCurrencyDisplay"
                        v-model="tempData.price">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Quantity"
                        v-model="tempData.qty">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Serial No.(If any)"
                        v-model="tempData.serial_no">
                    </v-text-field>
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date Supplied/Installed"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.date"
                        :max="new Date().toISOString().split('T')[0]"
                    />   
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Status"
                        v-model="tempData.status">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Recommendations"
                        v-model="tempData.recos">
                    </v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn 
                        @click="closeDialog"
                        color="red darken-1"
                        text>
                        Cancel
                    </v-btn>
                    <v-btn
                        @click="saveForm"
                        color="green darken-1"
                        text>
                        {{ isEditMode ? 'Update' : 'Submit' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import Snackbar from '../../Components/Snackbar.vue';

const snackbar = ref(null)

const headers = ref([
    { title:  'Description', value: 'description', align: 'center', sortable: true},
    { title: 'Price', value: 'formattedPrice', align: 'center'},
    { title:  'Quantity', value: 'qty', align: 'center'},
    { title:  'Serial No. (If any)', value: 'serial_no', align: 'center'},
    { title:  'Date Supplied/Installed', value: 'date_supplied', align: 'center'},
    { title:  'Status', value: 'status', align: 'center'},
    { title:  'Recommendations', value: 'recos', align: 'center'},
    { title:  'Actions', value: 'actions', align: 'center'},
])

const items = ref([])
const search = ref('')
const tempData = ref({})
const edit = ref({})
const dialog = ref(false)
const isEditMode = ref(false)

const saveForm = async () => {
    const to_update = { ...tempData.value }

    //convert formatted price (with commas) back to a number
    if(to_update.price) {
        //remove commas and any non-numeric characters except decimal point
        to_update.price = parseFloat(to_update.price.toString().replace(/[^\d.]/g, ''));
    }

    const url = isEditMode.value ? 'update_survey_equipment' : 'insert_survey_equipment'

    axios({
        method: 'post',
        url,
        data: { to_update }
    }).then(() => {
        if(isEditMode.value){
            snackbar.value.alertUpdate()
        }
        else{
            snackbar.value.alertSuccess()
        }
        fetchSurveyEquipments()
        dialog.value = false
        tempData.value = {}
    }).catch(() => {
        if(isEditMode.value){
            snackbar.value.alertCustom('There was an error updating your data.')
        }
        else{
            snackbar.value.alertError()
        }
    })
}

const closeDialog = () => {
    dialog.value = false
    tempData.value = {}
}

const openDialog = (mode, item = null) => {
    isEditMode.value = (mode === 'edit')
    if(mode === 'add'){
        resetForm()
        dialog.value = true
    }
    else if(mode === 'edit' && item){
        tempData.value = { ...item } 
        dialog.value = true
    }
}

const resetForm = () => {
    tempData.value = {}
    edit.value = null
    isEditMode.value = false
}

const getStatusColor = (status) => {
    const normalizedStatus = status.toUpperCase();
    if(normalizedStatus.includes('MISSING')){
        return 'red'
    }
    if(normalizedStatus.includes('NEW') || normalizedStatus.includes('UNUSED')){
        return 'blue'
    }
    if(normalizedStatus === 'ON USE'){
        return 'green'
    }
    return 'grey'
};

const formatCurrency = (value) => {
    if(!value && value !== 0) return '0.00';
    const num = parseFloat(value);
    if (isNaN(num)) return '0.00';
    
    return num.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const handleCurrencyInput = () => {
    if(tempData.value.price){
        //remove all non-numeric characters except decimal point
        let value = tempData.value.price.toString();
        value = value.replace(/[^\d.]/g, '');
        
        //ensure only one decimal point
        const parts = value.split('.');
        if(parts.length > 2){
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        tempData.value.price = value;
    }
};

const formatCurrencyDisplay = () => {
    if(tempData.value.price && tempData.value.price !== ''){
        const num = parseFloat(tempData.value.price);
        if(!isNaN(num)){
            tempData.value.price = formatCurrency(num);
        }
    }
};

const fetchSurveyEquipments = async () => {
    try{
        const res = await axios.get('/get_survey_equipments');
        items.value = res.data.map(item => ({
            ...item,
            formattedPrice: formatCurrency(item.price)  //use the actual item.price, not tempData
        }));
        console.log(res.data);
    }
    catch(error){
        console.error('Error fetching Survey data', error);
    }
}

onMounted(() => {
    fetchSurveyEquipments()
})
</script>
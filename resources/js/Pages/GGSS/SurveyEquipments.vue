<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
        <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">Survey Equipments</h1>
        <div class="flex-shrink-0">
            <v-tooltip location="left">
                <template v-slot:activator="{ props }">
                    <v-btn
                        @click="openIODialog('add')"
                        color="info"
                        icon
                        size="small"
                        v-bind="props">
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>
                <span>Add Incoming/Outgoing Equipments</span>
            </v-tooltip>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row lg:space-x-4 space-y-4 lg:space-y-0">
        <div class="flex-1 bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-2">List(s) of Equipments</h2>
            <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="searchList">
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
            <div class="overflow-x-auto">
                <v-data-table
                    class="font-sans mt-3"
                    density="compact"
                    fixed-footer
                    fixed-header
                    item-key="id"
                    :headers="headers"
                    :items="items"
                    :search="searchList">
                    <template v-slot:item.price="{ item }">
                        {{ formatCurrency(item.price) }}
                    </template>
                    <template v-slot:item.status="{ item }">
                        <v-chip :color="getStatusColor(item.status)" size="small" variant="flat">
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
            </div>
        </div>

        <div class="flex-1 bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-2">Incoming Equipments</h2>
            <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="searchIncoming">
                    </v-text-field>
                </div>
            </div>
            <div class="overflow-x-auto">
                <v-data-table
                    class="font-sans mt-3"
                    density="compact"
                    fixed-footer
                    fixed-header
                    item-key="id"
                    :headers="ioHeaders"
                    :items="incomingItems"
                    :search="searchIncoming">
                    <template v-slot:item.direction="{ item }">
                        <v-chip :color="item.direction === 'incoming' ? 'green' : 'orange'" size="small" variant="flat">
                            {{ item.direction }}
                        </v-chip>
                    </template>
                    <template v-slot:item.date="{ item }">
                        {{ item.date }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon
                                    @click="openIODialog('edit', item)"
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
            </div>
        </div>

        <div class="flex-1 bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-2">Outgoing Equipments</h2>
            <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="searchOutgoing">
                    </v-text-field>
                </div>
            </div>
            <div class="overflow-x-auto">
                <v-data-table
                    class="font-sans mt-3"
                    density="compact"
                    fixed-footer
                    fixed-header
                    item-key="id"
                    :headers="ioHeaders"
                    :items="outgoingItems"
                    :search="searchOutgoing">
                    <template v-slot:item.direction="{ item }">
                        <v-chip :color="item.direction === 'incoming' ? 'green' : 'orange'" size="small" variant="flat">
                            {{ item.direction }}
                        </v-chip>
                    </template>
                    <template v-slot:item.date="{ item }">
                        {{ item.date }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon
                                    @click="openIODialog('edit', item)"
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
            </div>
        </div>
    </div>
                                <!-- NOTE - start of add & edit dialog of list of equipments -->
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
                    v-model="tempData.date_supplied"
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

                                <!-- NOTE - start of add & edit dialog of incoming/outgoing equipments -->
    <v-dialog class="transition-discrete md:transition-normal" v-model="ioDialog" width="450" persistent no-click-animation>
        <v-card>
            <v-card-title style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;">{{ isEditingMode ? 'Edit' : 'Add' }}
            </v-card-title>
            <v-card-text>
                <v-autocomplete
                    density="compact"
                    hide-details
                    label="Direction"
                    :items="ioTypeOptions"
                    item-title="label"
                    item-value="value"
                    v-model="tempIOData.direction"
                    variant="outlined">
                </v-autocomplete>
                <v-autocomplete
                    class="mt-3"
                    hide-details
                    density="compact"
                    :items="items" 
                    item-title="description" 
                    item-value="description"
                    label="Description"
                    multiple
                    variant="outlined"
                    v-model="tempIOData.description">
                </v-autocomplete>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Site"
                    v-model="tempIOData.site">
                </v-text-field>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Handled By"
                    v-model="tempIOData.handled_by">
                </v-text-field>
                <v-date-input
                    class="mt-3"
                    clearable
                    density="compact"
                    hide-details
                    label="Date"
                    prepend-icon=""
                    prepend-inner-icon="mdi-calendar"
                    variant="outlined"
                    v-model="tempIOData.date"
                    :max="new Date().toISOString().split('T')[0]">
                </v-date-input>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn 
                    @click="closeIODialog"
                    color="red darken-1"
                    text>
                    Cancel
                </v-btn>
                <v-btn
                    @click="saveIOForm"
                    color="green darken-1"
                    text>
                    {{ isEditingMode ? 'Update' : 'Submit' }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref, computed } from 'vue';
import Snackbar from '../../Components/Snackbar.vue';

const snackbar = ref(null)

//SECTION - incoming/outgoing variables & functions
const ioHeaders = ref([
    // { title: 'Direction', value: 'direction', align: 'center', sortable: true },
    { title: 'Description', value: 'description', align: 'center', sortable: true },
    { title: 'Site', value: 'site', align: 'center' },
    { title: 'Handled By', value: 'handled_by', align: 'center' },
    { title: 'Date', value: 'date', align: 'center' },
    { title: 'Actions', value: 'actions', align: 'center' },
])

const ioItems = ref([])
const searchIncoming = ref('')
const searchOutgoing = ref('')
const ioDialog = ref(false)
const tempIOData = ref({})
const isEditingMode = ref(false)
const ioTypeOptions = [
    { label: 'Incoming', value: 'incoming' },
    { label: 'Outgoing', value: 'outgoing' },
]

//with unified date, we will rely on direction to split lists
const incomingItems = computed(() => ioItems.value.filter(it => it.direction === 'incoming'))
const outgoingItems = computed(() => ioItems.value.filter(it => it.direction === 'outgoing'))

const saveIOForm = async () => {
    const to_update = { ...tempIOData.value }

    //join the array of descriptions into a single string.
    if(Array.isArray(to_update.description)){
        to_update.description = to_update.description.join(', ');
    }
    
    //move unified date into backend fields for compatibility
    if(to_update.direction === 'incoming'){
        to_update.incoming_date = to_update.date || null
        to_update.outgoing_date = null
    }
    if(to_update.direction === 'outgoing'){
        to_update.outgoing_date = to_update.date || null
        to_update.incoming_date = null
    }

    //ensure description is properly set as array
    if(!to_update.description){
        to_update.description = []
    }
    else if(typeof to_update.description === 'string'){
        to_update.description = [to_update.description]
    }

    //format date only if they are not already formatted
    const dateFields = ['outgoing_date', 'incoming_date']
    dateFields.forEach(field => {
        if(to_update[field]){
            //if it's already in YYYY-MM-DD format, keep it as is
            if(typeof to_update[field] === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(to_update[field])){
                //already formatted correctly, no need to change
                return
            }
            
            //if it's a Date object or other format, convert it properly
            const d = new Date(to_update[field])
            if(!isNaN(d.getTime())){
                //use local date to avoid timezone issues
                const year = d.getFullYear()
                const month = String(d.getMonth() + 1).padStart(2, '0')
                const day = String(d.getDate()).padStart(2, '0')
                to_update[field] = `${year}-${month}-${day}`
            }
            else{
                to_update[field] = null
            }
        }
    })

    const url = isEditingMode.value ? 'update_incoming_outgoing_equipment' : 'insert_incoming_outgoing_equipment'

    axios({
        method: 'post',
        url,
        data: { to_update }
    }).then(() => {
        if(isEditingMode.value){
            snackbar.value.alertUpdate()
        }
        else{
            snackbar.value.alertSuccess()
        }
        fetchIOEquipments()
        ioDialog.value = false
        tempIOData.value = {}
    }).catch(() => {
        if(isEditingMode.value){
            snackbar.value.alertCustom('There was an error updating your data.')
        }
        else{
            snackbar.value.alertError()
        }
    })
}

const closeIODialog = () => {
    ioDialog.value = false
    tempIOData.value = {}
}

const openIODialog = (mode, item = null) => {
    isEditingMode.value = (mode === 'edit')
    if(mode === 'add'){
        resetIOForm()
        tempIOData.value.direction = 'incoming'
        tempIOData.value.description = []
        ioDialog.value = true
    }
    else if(mode === 'edit' && item){
        tempIOData.value = { ...item } 
        if(!tempIOData.value.direction){
            tempIOData.value.direction = tempIOData.value.incoming_date && !tempIOData.value.outgoing_date ? 'incoming' : 'outgoing'
        }
        //ensure description is an array for editing
        if(typeof tempIOData.value.description === 'string') {
            tempIOData.value.description = [tempIOData.value.description]
        }
        ioDialog.value = true
    }
}

const resetIOForm = () => {
    tempIOData.value = {
        description: []
    }
    edit.value = null
    isEditingMode.value = false
}

const fetchIOEquipments = async () => {
    try{
        const res = await axios.get('/get_incoming_outgoing_equipments');
        ioItems.value = res.data.map(item => ({
            ...item,
            //normalize to have date + direction for UI
            date: item.incoming_date || item.outgoing_date || null,
            direction: item.incoming_date && !item.outgoing_date ? 'incoming' : 'outgoing',
            //ensure description is available for display
            description: item.description || item.equipment_description || '',
        }));
        console.log(res.data);
    }
    catch(error){
        console.error('Error fetching incoming & outgoing equipments', error);
    }
}

//SECTION - list of equipments variables & functions
const headers = ref([
    { title:  'Description', value: 'description', align: 'center', sortable: true},
    { title: 'Price', value: 'price', align: 'center'},
    { title:  'Serial No. (If any)', value: 'serial_no', align: 'center'},
    { title:  'Date Supplied/Installed', value: 'date_supplied', align: 'center'},
    { title:  'Status', value: 'status', align: 'center'},
    { title:  'Recommendations', value: 'recos', align: 'center'},
    { title:  'Actions', value: 'actions', align: 'center'},
])

const items = ref([])
const search = ref('')
const searchList = ref('')
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

    //format date only if they are not already formatted
    const dateFields = ['date_supplied']
    dateFields.forEach(field => {
        if(to_update[field]){
            //if it's already in YYYY-MM-DD format, keep it as is
            if(typeof to_update[field] === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(to_update[field])){
                //already formatted correctly, no need to change
                return
            }
            
            //if it's a Date object or other format, convert it properly
            const d = new Date(to_update[field])
            if(!isNaN(d.getTime())){
                //use local date to avoid timezone issues
                const year = d.getFullYear()
                const month = String(d.getMonth() + 1).padStart(2, '0')
                const day = String(d.getDate()).padStart(2, '0')
                to_update[field] = `${year}-${month}-${day}`
            }
            else{
                to_update[field] = null
            }
        }
    })

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
        }))
        console.log(res.data);
    }
    catch(error){
        console.error('Error fetching Survey data', error);
    }
}

onMounted(() => {
    fetchSurveyEquipments()
    fetchIOEquipments()
})
</script>
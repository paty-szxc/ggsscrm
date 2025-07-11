<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
            <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">Office Supplies List</h1>
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
                                @click="openAddDialog()"
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
            :headers="headers"
            :items="items"
            :search="search"
            item-key="id"
            @dblclick:row="openEditDialog">
        </v-data-table>

        <v-dialog class="transition-discrete md:transition-normal" v-model="dialog" no-click-animation persistent width="700">
        <v-card>
            <v-card-title 
                style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;">
                {{ isEditMode ? 'Edit' : 'Add' }}
            </v-card-title>
            <v-card-text>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Item Name"
                    v-model="tempData.item_name">
                </v-text-field>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Cost"
                    v-model="tempData.item_cost">
                </v-text-field>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Quantity"
                    v-model="tempData.quantity">
                </v-text-field>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Unit"
                    v-model="tempData.unit">
                </v-text-field>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="C/O"
                    v-model="tempData.care_of">
                </v-text-field>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Remarks"
                    v-model="tempData.remarks">
                </v-text-field>
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
    </v-container>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import Snackbar from '../../Components/Snackbar.vue';

const search = ref('')
const headers = ref([
    { title: 'Item Name', value: 'item_name', align: 'center' },
    { title: 'Cost', value: 'item_cost', align: 'center' },
    { title: 'Quantity', value: 'quantity', align: 'center' },
    { title: 'Unit', value: 'unit', align: 'center' },
    { title: 'C/O', value: 'care_of', align: 'center' },
    { title: 'Remarks', value: 'remarks', align: 'center' },
])
const items = ref([])
const tempData = ref({})
const dialog = ref(false)
const isEditMode = ref(false)
const snackbar = ref(null)

// const submitForm = async () => {
//     const url = isEditMode.value ? 'update_office_supplies' : 'insert_office_supplies'

//     axios({
//         method: 'post',
//         url,
//         data: tempData.value 
//     }).then((res) => {
//         fetchOfficeSupplies()
//         if(isEditMode.value){
//             snackbar.value.alertUpdate()
//             console.log(res.data)
//         }
//         else{
//             snackbar.value.alertSuccess()
//             console.log(res.data)
//         }
//         dialog.value = false
//         tempData.value = {}
//     }).catch(() => {
//         if(isEditMode.value){
//             snackbar.value.alertCustom('There was an error updating your data.')
//         }
//         else{
//             snackbar.value.alertError()
//         }
//     })
// }

const submitForm = async () => {
    try{
        const url = isEditMode.value ? 'update_office_supplies' : 'insert_office_supplies';
        const res = await axios({
            method: 'post',
            url,
            data: tempData.value
        });

        if(res.data.success){
            fetchOfficeSupplies()
            if(isEditMode.value){
                snackbar.value.alertUpdate()
            } 
            else{
                snackbar.value.alertSuccess()
            }
            dialog.value = false
            tempData.value = {}
        } 
        else{
            snackbar.value.alertError()
        }
    }
    catch(err){
        snackbar.value.alertWarning('Validation failed. Please check your inputs.', err);
    }
}

const openEditDialog = (event, { item }) => {
    tempData.value = { ...item }
    isEditMode.value = true
    dialog.value = true
}

const openAddDialog = () => {
    isEditMode.value = false
    dialog.value = true
}

const closeAddDialog = () => {
    tempData.value = {}
    dialog.value = false
    fetchOfficeSupplies()
}

const fetchOfficeSupplies = async () => {
    try{
        const res = await axios.get('get_office_supplies')
        items.value = res.data.map(item => ({
            ...item,
        }))
    } 
    catch(error){
        console.error('Error fetching data', error)
    }
}

onMounted(() => {
    fetchOfficeSupplies()
})
</script>
<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
            <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">Company Assets</h1>
        </div>
        <!-- start of house & lot table -->
        <div class="flex flex-col sm:flex-row gap-4 bg-white rounded-lg shadow p-4">
            <div class="flex-1 bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold mb-2">House & Lot</h2>
                <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="searchHaL">
                    </v-text-field>
                </div>
                <div class="flex-shrink-0">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                @click="addHaLDialog"
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
                        :headers="houseHeaders"
                        :items="houses"
                        :search="searchHaL"
                        class="min-w-[400px]">
                    </v-data-table>
                </div>
            </div>

            <v-dialog class="transition-discrete md:transition-normal" persistent v-model="halDialog" width="400px" no-click-animation>
                <v-card>
                    <v-card-title 
                        style="background: linear-gradient(135deg, #0047AB, #50C878); 
                        color: white;">Add
                    </v-card-title>
                    <v-card-text>
                        <v-text-field
                            hide-details
                            label="Address"
                            v-model="house_address">
                        </v-text-field>
                        <v-text-field
                            class="mt-3"
                            hide-details
                            label="Cost"
                            v-model="house_cost">
                        </v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn 
                            @click="closeAddHaLDialog"
                            color="red darken-1"
                            text>
                            Cancel
                        </v-btn>
                        <v-btn
                            @click="submitHaL"
                            color="green darken-1"
                            text>
                            Submit
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- start of company  vehicle table -->
            <div class="flex-1 bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Company Vehicle</h2>
                <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="searchVC">
                    </v-text-field>
                </div>
                <div class="flex-shrink-0">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                @click="addVehicleDialog"
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
                        :headers="vehicleHeaders"
                        :items="vehicles"
                        :search="searchVC"
                        class="min-w-[400px]">
                    </v-data-table>
                </div>
            </div>

            <v-dialog class="transition-discrete md:transition-normal" persistent v-model="vehicleDialog" width="400px" no-click-animation>
                <v-card>
                    <v-card-title 
                        style="background: linear-gradient(135deg, #0047AB, #50C878); 
                        color: white;">Add
                    </v-card-title>
                    <v-card-text>
                        <v-text-field
                            hide-details
                            label="Address"
                            v-model="vehicle">
                        </v-text-field>
                        <v-text-field
                            class="mt-3"
                            hide-details
                            label="Cost"
                            v-model="vehicle_cost">
                        </v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn 
                            @click="closeAddVehicleDialog"
                            color="red darken-1"
                            text>
                            Cancel
                        </v-btn>
                        <v-btn
                            @click="submitVehicle"
                            color="green darken-1"
                            text>
                            Submit
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>

        <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import Snackbar from '../../Components/Snackbar.vue'

const houseHeaders = ref([
    { title: 'Address', value: 'address', align: 'center'},
    { title: 'Cost', value: 'formattedCost', align: 'center'},
])
const houses = ref([])
const house_address = ref('')
const house_cost = ref('')
const searchHaL = ref('')

const vehicleHeaders = ref([
    { title: 'Vehicle Name', value: 'vehicle_name', align: 'center'},
    { title: 'Cost', value: 'formattedCost', align: 'center'},
])
const vehicles = ref([])
const vehicle = ref('')
const vehicle_cost = ref('')
const searchVC = ref('')

const snackbar = ref(null)
const halDialog = ref(false)
const vehicleDialog = ref(false)

const submitVehicle = async () => { //submit button for company vehicle
    try{
        const res = await axios({
            method: 'post',
            url: 'insert_company_vehicle',
            data: {
                vehicle_name: vehicle.value,
                cost: vehicle_cost.value
            }
        })

        if(res.data.success){
            fetchCompanyVehicle()
            snackbar.value.alertSuccess(res.data.message)
            vehicleDialog.value = false
            vehicle.value = ''
            vehicle_cost.value = ''
        }
        else{
            snackbar.value.alertError(res.data.message)
        }
    }
    catch(err){
        snackbar.value.alertWarning('Validation failed.', err)
    }
}

const closeAddVehicleDialog = () => {
    vehicleDialog.value = false
}

const addVehicleDialog = () => {
    vehicleDialog.value = true
    vehicle.value = ''
    vehicle_cost.value = ''
}

const submitHaL = async () => { //submit button for house and lot
    try{
        const res = await axios({
            method: 'post',
            url: 'insert_house_and_lot',
            data: {
                address: house_address.value,
                cost: house_cost.value
            }
        })

        if(res.data.success){
            fetchHouseAndLot()
            snackbar.value.alertSuccess(res.data.message)
            halDialog.value = false
            house_address.value = ''
            house_cost.value = ''
        }
        else{
            snackbar.value.alertError(res.data.message)
        }
    }
    catch(err){
        snackbar.value.alertWarning('Validation failed. Please check your inputs.', err)
    }
}

const closeAddHaLDialog = () => {
    halDialog.value = false
}

const addHaLDialog = () => {
    halDialog.value = true
    house_address.value = ''
    house_cost.value = ''
}

const formatCurrency = (value) => {
    //convert to number first
    const num = parseFloat(value);
    
    //return empty string for 0, null, undefined, or NaN
    if (!num || isNaN(num)) return '';
    
    //format valid numbers
    return `₱ ${num.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
};

const fetchHouseAndLot = async () => {
    try{
        const res = await axios.get('get_house_and_lot')
        houses.value = res.data.map(item => ({
            ...item,
            formattedCost: formatCurrency(item.cost),
            originalCost: item.cost //keep original value if needed
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}
const fetchCompanyVehicle = async () => {
    try{
        const res = await axios.get('get_company_vehicle')
        vehicles.value = res.data.map(item => ({
            ...item,
            formattedCost: formatCurrency(item.cost),
            originalCost: item.cost //keep original value if needed
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

onMounted(() => {
    fetchCompanyVehicle()
    fetchHouseAndLot()
})
</script>
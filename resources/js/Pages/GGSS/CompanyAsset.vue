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
                                @click="openHaLDialog('add')"
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
                        <template v-slot:item.pdf_attachment="{ item }">
                            <div v-if="item.pdf_url" class="pdf-attachment-wrapper">
                                <v-tooltip bottom>
                                <template v-slot:activator="{ props }">
                                    <v-btn
                                    v-bind="props"
                                    color="primary"
                                    variant="text"
                                    size="large"
                                    class="!normal-case"
                                    @click="openPdfViewer(item.pdf_url)"
                                    >
                                    <v-icon start color="red">mdi-file-pdf-box</v-icon>
                                    <span class="ml-2">{{ getFileName(item.pdf_url) }}</span>
                                    </v-btn>
                                </template>
                                <span>Click to view PDF</span>
                                </v-tooltip>
                            </div>
                            <span v-else class="text-grey">No attachment</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                size="small"
                                class="me-2"
                                color="info"
                                @click="openHaLDialog('edit', item)">
                                mdi-pencil
                            </v-icon>
                        </template>
                    </v-data-table>
                </div>
            </div>

            <v-dialog class="transition-discrete md:transition-normal" persistent v-model="halDialog" width="400px" no-click-animation>
                <v-card>
                    <v-card-title 
                        style="background: linear-gradient(135deg, #0047AB, #50C878);
                        color: white;">
                        {{ halDialogTitle }}
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
                        <v-file-input
                            class="mt-3"
                            density="compact"
                            label="PDF Attachment"
                            v-model="house_pdf_file"
                            accept=".pdf"
                            prepend-icon=""
                            prepend-inner-icon="mdi-file-pdf-box"
                            hide-details
                            clearable
                            variant="outlined"
                            :rules="[v => !v || v.length === 0 || v[0].size < 2000000 || 'PDF size should be less than 2 MB!']"
                        ></v-file-input>
                        <div v-if="house_current_pdf_url && halDialogMode === 'edit'" class="mt-2 text-sm text-gray-600">
                            Current PDF:
                            <v-btn
                                color="primary"
                                variant="text"
                                size="small"
                                class="!normal-case"
                                @click="openPdfViewer(house_current_pdf_url)">
                                <v-icon start>mdi-file-pdf-box</v-icon> View {{ getFileName(house_current_pdf_url) }}
                            </v-btn>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn 
                            @click="closeHaLDialog"
                            color="red darken-1"
                            text>
                            Cancel
                        </v-btn>
                        <v-btn
                            @click="submitHaL"
                            color="green darken-1"
                            text>
                            {{ halDialogMode === 'add' ? 'Submit' : 'Update' }}
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
                            label="Vehicle"
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

        <!-- PDF Viewer Dialog -->
        <v-dialog v-model="pdfViewerDialog" fullscreen>
            <v-card>
                <v-toolbar style="background: linear-gradient(135deg, #0047AB, #50C878); 
                    color: white;">
                    <v-toolbar-title>PDF Preview</v-toolbar-title>
                    <v-toolbar-items>
                        <v-btn
                            icon
                            @click="pdfViewerDialog = false">
                            <v-icon color="red">mdi-close</v-icon>
                    </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text class="flex-grow pa-0">
                    <iframe v-if="currentPdfUrl" :src="currentPdfUrl" width="100%" height="100%" frameborder="0"></iframe>
                    <div v-else class="flex items-center justify-center h-full text-lg text-gray-500">
                        No PDF available for preview.
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>

        <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref, computed } from 'vue'
import Snackbar from '../../Components/Snackbar.vue'

const houseHeaders = ref([
    { title: 'Address', value: 'address', align: 'center'},
    { title: 'Cost', value: 'formattedCost', align: 'center'},
    { title: 'Attachment', value: 'pdf_attachment', align: 'center'},
    { title: 'Actions', value: 'actions', align: 'center'}
])
const houses = ref([])
const house_address = ref('')
const house_cost = ref('')
const searchHaL = ref('')
const house_pdf_file = ref(null)
const house_current_pdf_url = ref(null)
const currentPdfUrl = ref(null)
const pdfViewerDialog = ref(false)
const halDialogMode = ref('add')
const editingHaL = ref(null)

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

const getFileName = (url) => {
    return url.split('/').pop() || 'document.pdf'
}

const openPdfViewer = (url) => {
    currentPdfUrl.value = url;
    pdfViewerDialog.value = true
};

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

// const submitHaL = async () => { //submit button for house and lot
//     try{
//         const res = await axios({
//             method: 'post',
//             url: 'insert_house_and_lot',
//             data: {
//                 address: house_address.value,
//                 cost: house_cost.value
//             }
//         })

//         if(res.data.success){
//             fetchHouseAndLot()
//             snackbar.value.alertSuccess(res.data.message)
//             halDialog.value = false
//             house_address.value = ''
//             house_cost.value = ''
//         }
//         else{
//             snackbar.value.alertError(res.data.message)
//         }
//     }
//     catch(err){
//         snackbar.value.alertWarning('Validation failed. Please check your inputs.', err)
//     }
// }

const submitHaL = async () => {
    const formData = new FormData();
    formData.append('address', house_address.value);
    formData.append('cost', house_cost.value !== null ? String(house_cost.value) : '');
    
    // Corrected logic for appending the PDF file
 // Ensure you're properly handling the file input
    const fileToAppend = house_pdf_file.value instanceof File 
        ? house_pdf_file.value 
        : (house_pdf_file.value?.[0] instanceof File ? house_pdf_file.value[0] : null);

        if (fileToAppend) {
            formData.append('pdf_file', fileToAppend);
            console.log('Appending PDF file:', fileToAppend.name);
        } else {
            console.log('No new PDF file selected for upload or file object is invalid.');
        }

        
        if (halDialogMode.value === 'edit') {
            // Crucial for Laravel to interpret this as a PUT request
            formData.append('_method', 'PUT'); 
        }

        // Log FormData contents (for debugging)
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }


    try {
        let res;
        if (halDialogMode.value === 'add') {
            res = await axios.post('insert_house_and_lot', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
        } else {
            // Pass the ID in the URL for update operations
            // res = await axios.post(`update_house_and_lot/${editingHaL.value.id}`, formData, { 
            //     headers: { 'Content-Type': 'multipart/form-data' },
            // });
            res = await axios.post(`update_house_and_lot/${editingHaL.value.id}`, formData, { 
            headers: { 
                'Content-Type': 'multipart/form-data',
                'X-HTTP-Method-Override': 'PUT' // Alternative to _method
            },
        });
        }

        if (res.data.success) {
            fetchHouseAndLot()
            snackbar.value.alertSuccess(res.data.message)
            closeHaLDialog()
        } else {
            if (res.data.errors) {
                const errorMessages = Object.values(res.data.errors).flat().join('\n');
                snackbar.value.alertWarning(`Validation failed:\n${errorMessages}`);
            } else {
                snackbar.value.alertError(res.data.message)
            }
        }
    } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
            const errorMessages = Object.values(err.response.data.errors).flat().join('\n');
            snackbar.value.alertWarning(`Validation failed. Please check your inputs:\n${errorMessages}`);
        } else {
            snackbar.value.alertWarning('An error occurred. Please try again later.');
            console.error('Error submitting House & Lot data:', err);
        }
    }
}

const halDialogTitle = computed(() => {
    return halDialogMode.value === 'add' ? 'Add House & Lot' : 'Edit House & Lot';
});

const closeHaLDialog = () => {
    halDialog.value = false
    house_address.value = ''
    house_cost.value = ''
    house_pdf_file.value = null
    house_current_pdf_url.value = null
    editingHaL.value = null
    halDialogMode.value = 'add'; // Reset mode to 'add' for next time
}

const openHaLDialog = (mode, item = null) => {
    halDialogMode.value = mode; // Set the mode here!
    halDialog.value = true;
    house_pdf_file.value = null; // Clear previous file selection

    if (mode === 'edit' && item) {
        console.log(item);
        
        editingHaL.value = item;
        house_address.value = item.address;
        house_cost.value = item.originalCost;
        house_current_pdf_url.value = item.pdf_url; // Set the current PDF URL for display
    } else {
        editingHaL.value = null;
        house_address.value = '';
        house_cost.value = '';
        house_current_pdf_url.value = null; // Clear current PDF URL for add mode
    }
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

<style scoped>
.pdf-attachment-wrapper{
    display: inline-flex;
    align-items: center;
}
</style>
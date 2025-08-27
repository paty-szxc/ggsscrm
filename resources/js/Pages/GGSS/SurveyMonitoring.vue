<template>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
        <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">List of Survey(s)</h1>
        <div v-if="currentUser && (currentUser.role === 'Admin')" class="ml-6 mb-4">
            <v-checkbox
                v-model="showAll"
                label="Show all data"
                hide-details
                density="compact"
                class="mr-2"
                @change="fetchSurveyData"
            />
        </div>
    </div>

    <SurveyTable
        :headers="headers"
        :survey_data="surveyData"
        @refresh-data="fetchSurveyData"
        @open-dialog="openAddDialog"
        @edit-data="openEditDialog"
        @open-pdf-dialog="openPdfDialog">
    </SurveyTable>
                                        <!-- NOTE - ADD & EDIT DIALOG -->
    <v-dialog class="transition-discrete md:transition-normal" v-model="dialog" no-click-animation persistent width="700">
        <v-card>
            <v-card-title 
                style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;">
                {{ isEditMode ? 'Edit' : 'Add' }}
            </v-card-title>
            <v-card-text>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 item-start">
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date Started"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.date_started"
                    />
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Location"
                        v-model="tempData.location">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Survey Details"
                        v-model="tempData.survey_details">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Area"
                        v-model="tempData.area">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Processed By"
                        v-model="tempData.processed_by">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Surveyed By"
                        v-model="tempData.surveyed_by">
                    </v-text-field>
                    <div style="display: flex; align-items: center;">
                        <v-checkbox
                            color="primary"
                            direction="horizontal" 
                            density="compact" 
                            hide-details 
                            v-model="tempData.survey"
                            label="Survey">
                        </v-checkbox>
                        <!-- <v-checkbox
                            class="ms-8"
                            color="primary"
                            direction="horizontal" 
                            density="compact" 
                            hide-details 
                            v-model="tempData.data_process"
                            label="Start">
                        </v-checkbox>
                        <v-checkbox
                            class="ms-8"
                            color="primary"
                            direction="horizontal" 
                            density="compact" 
                            hide-details 
                            v-model="tempData.plans"
                            label="End">
                        </v-checkbox> -->
                    </div>   
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Start Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.data_process"
                    />            
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="End Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.plans"
                    />            
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date Approved"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.date_approved"
                    />
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date Completed/Delivered"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.date_completed"
                    />
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Remarks"
                        v-model="tempData.remarks">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Contact Person"
                        v-model="tempData.contact_person">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Contact No."
                        v-model="tempData.contact_no">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        label="Thru"
                        v-model="tempData.thru">
                    </v-text-field>
    
                    <!-- <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date Delivered"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="tempData.date_delivered"
                    /> -->
    
                    <v-file-input
                        class="mt-3"
                        density="compact"
                        variant="outlined"
                        prepend-icon=""
                        prepend-inner-icon="mdi-attachment"
                        v-model="internalSelectedFiles" 
                        id="file-upload"
                        type="file"
                        multiple
                        label="File Upload"
                        @change="handleFileChange"
                        accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                    />
                </div>

                <div v-if="selectedFiles.length > 0" class="mt-4 text-left">
                    <p class="text-gray-700 font-medium mb-2">Selected Files:</p>
                    <ul class="list-disc list-inside text-gray-600">
                    <li v-for="(file, index) in selectedFiles" :key="index" class="text-sm">
                        {{ file.name }} ({{ (file.size / 1024 / 1024).toFixed(2) }} MB)
                        <v-icon @click="removeFile(index)">mdi-close-circle</v-icon>
                    </li>
                    </ul>
                </div>

                <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Uploaded Files</h3>
                <div v-if="files.length === 0" class="text-center text-gray-500 p-4 border rounded-lg bg-gray-50">
                    No files uploaded yet.
                </div>
                <div v-else class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                        <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Icon
                        </th> -->
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Filename
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Size
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Uploaded At
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="file in files" :key="file.id" class="hover:bg-gray-50">
                        <!-- <td class="px-6 py-4 whitespace-nowrap">
                            <span v-html="getFileIcon(file.mime_type)" class="text-2xl"></span>
                        </td> -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ file.original_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ (file.size / 1024).toFixed(2) }} KB
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(file.created_at).toLocaleString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-btn
                                        color="#0288D1"
                                        density="compact"
                                        fab
                                        icon="mdi-eye"
                                        variant="flat"
                                        v-bind="props"
                                        @click="openFileViewerDialog(file)">
                                    </v-btn>
                                </template>
                                <span>View</span>
                            </v-tooltip>
                            <!-- <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-btn
                                        @click="removeUploadedFile"
                                        class="ml-4"
                                        density="compact"
                                        fab
                                        icon="mdi-delete"
                                        x-small
                                        color="red"
                                        v-bind="props">
                                    </v-btn>
                                </template>
                                <span>Delete</span>
                            </v-tooltip> -->
                            <!-- <a :href="getFileUrl(file.path)" download class="text-green-600 hover:text-green-900">Download</a> -->
                        </td>
                        </tr>
                    </tbody>
                    </table>
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
                                    <!-- NOTE - PDF DIALOG -->
    <v-dialog class="transition-discrete md:transition-normal" v-model="pdfDialog" no-click-animation persistent width="700">
        <v-card>
            <v-card-title 
                style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;">
                Files for Survey
            </v-card-title>
            <v-card-text>
                <div v-if="pdfFiles.length === 0" class="text-center text-gray-500 p-4 border rounded-lg bg-gray-50">
                    No files available for this survey.
                </div>
                <div v-else class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Filename
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Size
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Uploaded At
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="file in pdfFiles" :key="file.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ file.original_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ (file.size / 1024).toFixed(2) }} KB
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(file.created_at).toLocaleString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <v-btn
                                        class="text-none text-subtitle-1"
                                        color="#0288D1"
                                        size="small"
                                        variant="flat"
                                        @click="openFileViewerDialog(file)">
                                        View
                                    </v-btn>
                                    <!-- <a :href="getFileUrl(file.path)" download class="text-green-600 hover:text-green-900">Download</a> -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn 
                    @click="closePdfDialog"
                    color="red darken-1"
                    text>
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

                                        <!-- NOTE - PDF VIEWER DIALOG -->
    <v-dialog v-model="pdfViewerDialog" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-card>
                <v-toolbar style="background: linear-gradient(135deg, #0047AB, #50C878);">
                    <v-btn 
                        @click="closePdfViewerDialog"
                        color="black"
                        text>
                        Close
                    </v-btn>
                    <v-toolbar-title>{{ currentFile ? currentFile.original_name : 'File Viewer' }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-card-text class="pa-0 d-flex align-center justify-center" style="height: calc(100vh - 64px); background-color: #f0f0f0;">
                    <div v-if="currentFile" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                        <iframe
                            v-if="currentFile.mime_type === 'application/pdf'"
                            :src="getFileUrl(currentFile.path)"
                            frameborder="0"
                            width="100%"
                            height="100%"
                            style="border: none;">
                            This browser does not support PDFs. Please download the PDF to view it.
                        </iframe>
                        <img
                            v-else-if="currentFile.mime_type.startsWith('image/')"
                            :src="getFileUrl(currentFile.path)"
                            alt="Image Preview"
                            style="max-width: 100%; max-height: 100%; object-fit: contain;"
                        />
                        <div v-else class="text-center pa-5">
                            <v-icon size="64">mdi-alert-circle-outline</v-icon>
                            <p class="mt-2">Unsupported file type for inline viewing.</p>
                            <v-btn class="mt-4" :href="getFileUrl(currentFile.path)" download color="primary">Download File</v-btn>
                        </div>
                    </div>
                    <div v-else class="text-center pa-5">
                        No file selected for viewing.
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>
    
    <Snackbar ref="snackbar"></Snackbar>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SurveyTable from '../../Components/SurveyTable.vue';
import Snackbar from '../../Components/Snackbar.vue';

const headers = ref([
    { title: 'Date Started', value: 'date_started', align: 'center' },
    { title: 'Location & Client Name', value: 'location', align: 'center' },
    { title: 'Type of Survey', value: 'survey_details', align: 'center' },
    { title: 'Area', value: 'area', align: 'center' },
    { title: 'Processed By', value: 'processed_by' },
    { title: 'Surveyed By', value: 'surveyed_by' },
    { title: 'Survey', value: 'survey', align: 'center' },
    { title: 'Making of Plans (Data Process)', align: 'center', children: [
        { title: 'Start', value: 'data_process', align: 'center' },
        { title: 'End', value: 'plans', align: 'center' },
    ]},
    { title: 'Date Approved', value: 'date_approved', align: 'center' },
    { title: 'Date Completed/Delivered', value: 'date_completed', align: 'center' },
    // { title: 'Remarks', value: 'remarks', align: 'center' },
    { title: 'Attachment(s) ', value: 'files', align: 'center' }
])

const snackbar = ref(null)

const surveyData = ref([])
const tempData = ref({})
const edit = ref({})
const dialog = ref(false)
const isEditMode = ref(false)
const currentUser = ref(null)
const showAll = ref(true) //default to showing all data

// Reactive variables
const selectedFiles = ref([]); // Stores files selected by the user for upload
const files = ref([]); // Stores the list of files already uploaded and fetched from backend
const isUploading = ref(false); // Flag to indicate if an upload is in progress
const message = ref(''); // Message to display to the user (success/error)
const messageType = ref(''); // Type of message ('success' or 'error')
const internalSelectedFiles = ref([]); 

const pdfDialog = ref(false); // Make sure these are defined
const pdfFiles = ref([]);   // Make sure these are defined

// NEW REFS FOR PDF VIEWER DIALOG
const pdfViewerDialog = ref(false); // Still used for the dialog visibility
const currentFile = ref(null);

// NEW FUNCTIONS FOR PDF VIEWER DIALOG
const openFileViewerDialog = (file) => { // Takes 'file' (can be PDF or image)
    currentFile.value = file; // Assign the selected file
    pdfViewerDialog.value = true; // Open the viewer dialog
};

const closePdfViewerDialog = () => {
    pdfViewerDialog.value = false;
    currentPdfFile.value = null; // Clear the selected file when closing
};

const getFileUrl = (filePath) => {
    const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin;
    return `${baseUrl}/storage/${filePath}`;
};

const closePdfDialog = () => {
    pdfDialog.value = false;
    pdfFiles.value = [];
};

const openPdfDialog = (item) => {
    console.log('Opening file list dialog for item:', item);
    if (item && item.id && item.files) {
        // Filter to include PDF, JPG, and PNG files
        pdfFiles.value = item.files.filter(file => 
            file.mime_type === 'application/pdf' ||
            file.mime_type === 'application/docx'||
            file.mime_type === 'image/jpeg' ||
            file.mime_type === 'image/png' ||
            file.mime_type === 'application/doc' ||
            file.mime_type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' 

        );
        pdfDialog.value = true;
    } else {
        console.warn('Cannot open file list dialog: item or item.files is missing.', item);
        snackbar.value.alertCustom('No files found for this entry or data is incomplete.');
    }
};

const handleFileChange = () => {
    message.value = '';
    messageType.value = '';
    internalSelectedFiles.value.forEach(newFile => {
        const isDuplicate = selectedFiles.value.some(
            existingFile => existingFile.name === newFile.name && existingFile.size === newFile.size
        );
        if (!isDuplicate) {
            selectedFiles.value.push(newFile);
        }
    });
    internalSelectedFiles.value = [];
    console.log("All accumulated files:", selectedFiles.value);
};


//function to upload selected files
const uploadFiles = async () => {
    console.log(selectedFiles.value, 'selectedFiles')
    const to_update = { ...tempData.value };

    console.log(to_update.id ,'TESTCONSOLEEE')

    if (selectedFiles.value.length === 0) {
        message.value = 'Please select files to upload.';
        messageType.value = 'error';
        return;
    }

    isUploading.value = true; 
    message.value = ''; 
    messageType.value = '';

    const formData = new FormData(); 
    selectedFiles.value.forEach(file => {
        formData.append('files[]', file); 
    });

    if(to_update.id){
        formData.append('survey_project_id', to_update.id)
    }

    try {
        const response = await axios.post('/api/files/upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
            },
        });
        fetchSurveyData()
        message.value = response.data.message;
        messageType.value = 'success';
        selectedFiles.value = []; 

        // fetchFiles(); 
    } catch (error) {
        // Handle upload errors
        console.error('Error uploading files:', error);
        if (error.response && error.response.data && error.response.data.message) {
        message.value = error.response.data.message; 
        } else {
        message.value = 'An error occurred during file upload.';
        }
        messageType.value = 'error';
    } finally {
        isUploading.value = false; // Reset uploading flag
    }
};

const getFileIcon = (filePath) => {
    // Ensure you have a leading slash if your path from the backend doesn't include it
    // Example: if filePath is 'surveys/file.pdf', you want '/storage/surveys/file.pdf'
    // If your backend always returns paths like 'storage/surveys/file.pdf', this is fine.
    const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin;
    // Check if filePath already contains 'storage/' to avoid double slashes
    if (filePath && !filePath.startsWith('storage/')) {
        return `${baseUrl}/storage/${filePath}`;
    }
    return `${baseUrl}/${filePath}`;
};

// const removeFile = (index) => {
//     selectedFiles.value.splice(index, 1);
// };

// const removeUploadedFile = async (fileId) => {
//     if (!confirm('Are you sure you want to delete this uploaded file? This action cannot be undone.')) {
//         return; // User cancelled
//     }

//     try {
//         // Correct API call using axios.delete and the correct route
//         const response = await axios.post(`/api/files/delete/${fileId}`); 
//         snackbar.value.alertDelete(response.data.message || 'File deleted successfully!');
        
//         // Remove the file from the 'files' reactive array to update the UI instantly
//         files.value = files.value.filter(file => file.id !== fileId);
        
//         // Optionally, re-fetch all survey data to ensure consistency across the application
//         fetchSurveyData(); 

//     } catch (error) {
//         console.error('Error deleting file:', error);
//         if (error.response && error.response.data && error.response.data.message) {
//             snackbar.value.alertCustom(error.response.data.message);
//         } else {
//             snackbar.value.alertCustom('Failed to delete file.');
//         }
//     }
// };

const fetchCurrentUser = async () => {
    try{
        const res = await axios.get('/get_current_user_for_survey')
        currentUser.value = res.data
        console.log(res.data)
    } 
    catch(error){
        console.error('Error fetching user data', error)
    }
}

const submitForm = async () => {
    //make a copy so you don't mutate the form state
    const to_update = { ...tempData.value };

    //convert booleans to integers
    to_update.survey = !!to_update.survey ? 1 : 0;
    // to_update.data_process = !!to_update.data_process ? 1 : 0;
    // to_update.plans = !!to_update.plans ? 1 : 0;

    //format dates only if they are not already formatted
    const dateFields = ['date_started', 'date_completed', 'data_process', 'plans', 'date_approved', 'date_delivered'];
    dateFields.forEach(field => {
        if (to_update[field]) {
            //if it's already in YYYY-MM-DD format, keep it as is
            if (typeof to_update[field] === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(to_update[field])) {
                //already formatted correctly, no need to change
                return;
            }
            
            //if it's a Date object or other format, convert it properly
            const d = new Date(to_update[field]);
            if (!isNaN(d.getTime())) {
                //use local date to avoid timezone issues
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                to_update[field] = `${year}-${month}-${day}`;
            } else {
                to_update[field] = null;
            }
        }
    });

    uploadFiles()

    const url = isEditMode.value ? 'update_survey_data' : 'insert_survey_data'

    axios({
        method: 'post',
        url,
        data: { to_update }
    }).then(() => {
        fetchSurveyData()
        if(isEditMode.value){
            snackbar.value.alertUpdate()
        } else {
            snackbar.value.alertSuccess()
        }
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

const openEditDialog = (item) => {
    fetchSurveyData()
    tempData.value = item
    isEditMode.value = true
    dialog.value = true
    edit.value = { ...item }
    tempData.value.survey = item.survey == 1 ? true : false
    files.value = item.files || []
    selectedFiles.value = []
    internalSelectedFiles.value = []
    // tempData.value.data_process = item.data_process == 1 ? true : false
    // tempData.value.plans = item.plans == 1 ? true : false
}

const openAddDialog = () => {
    isEditMode.value = false
    dialog.value = true
    tempData.value = {} //clear previous data
    files.value = [] //no uploaded files for new entry
    selectedFiles.value = []; //clear new selections
    internalSelectedFiles.value = [];

    if(currentUser.value){
        const excludeEmpsCodes = ['0000-0000', '0001-0000', '0000-0003', '0000-0005', '0000-0012', '0002-0000'];
        const normalizedId = String(currentUser.value.emp_code).trim().toLowerCase();
        const isExcluded = excludeEmpsCodes.map(code => code.toLowerCase()).includes(normalizedId);

        if(isExcluded){
            tempData.value.processed_by = currentUser.value.username
            tempData.value.surveyed_by = currentUser.value.username
        } 
        else{
            tempData.value.processed_by = `Engr. ${currentUser.value.username}`
            tempData.value.surveyed_by = `Engr. ${currentUser.value.username}`
        }
        console.log(currentUser.value.username)
    }
}

const closeAddDialog = () => {
    tempData.value = {}
    dialog.value = false
    fetchSurveyData()
}

const fetchSurveyData = async () => {
    try{
        let params = {};
        if(currentUser.value && (currentUser.value.role === 'Admin')){
            params.show_all = showAll.value ? 1 : 0;
        }
        const res = await axios.get('/get_survey_data', { params });
        surveyData.value = res.data;
        console.log(res.data);
    }
    catch(error){
        console.error('Error fetching Survey data', error);
    }
}


onMounted(() => {
    fetchSurveyData()
    fetchCurrentUser()
});
</script>

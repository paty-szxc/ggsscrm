<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
            <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">Lot Titles</h1>
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
                    <v-tooltip type_of_survey="bottom" v-if="!userIsViewer">
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
                    <v-tooltip location="left" v-else>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                color="grey"
                                icon
                                size="small"
                                v-bind="props"
                                style="cursor: not-allowed;">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Restricted: Authorized users only can add.</span>
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
            :items="plans"
            :search="search">
            <template v-slot:item.plan_attachment="{ item }">
                <div v-if="item && Array.isArray(item.plan_attachment)">
                    <div v-for="(fileUrl, index) in item.plan_attachment" :key="index">
                        <a
                        :href="fileUrl"
                        :download="getFileName(item, fileUrl, index)"
                        class="text-decoration-none">
                        <v-icon color="blue">mdi-file-cad</v-icon>
                        <span class="ml-2">{{ getFileName(item, fileUrl, index) }}</span>
                        </a>
                    </div>
                </div>
                <div v-else-if="item && item.plan_attachment">
                    <a
                        :href="item.plan_attachment"
                        :download="getFileName(item, item.plan_attachment, 0)"
                        class="text-decoration-none">
                        <v-icon color="blue">mdi-file-cad</v-icon>
                        <span class="ml-2">{{ getFileName(item, item.plan_attachment, 0) }}</span>
                    </a>
                </div>
                <span v-else class="text-grey">No attachment</span>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-tooltip location="bottom" v-if="!userIsViewer">
                    <template v-slot:activator="{ props }">
                        <v-icon
                            @click="openDialog('edit', item && item.raw ? item.raw : item)"
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
                <v-tooltip location="bottom" v-else>
                    <template v-slot:activator="{ props }">
                        <v-icon
                            size="small"
                            class="me-2"
                            color="grey"
                            v-bind="props"
                            style="cursor: not-allowed;"> mdi-pencil
                        </v-icon>
                    </template>
                    <span>Restricted: Authorized users only can edit.</span>
                </v-tooltip>
            </template>
        </v-data-table>

                                                <!-- NOTE - start of add & edit dialog -->
        <v-dialog class="transition-discrete md:transition-normal" persistent v-model="dialog" width="500px" no-click-animation>
            <v-card>
                <v-card-title 
                    style="background: linear-gradient(135deg, #0047AB, #50C878); 
                    color: white;"> {{ isEditMode === 'add' ? 'Add' : 'Edit' }}
                </v-card-title>
                <v-card-text>
                    <v-date-input
                        class="mt-3"
                        clearable
                        density="compact"
                        hide-details
                        label="Date of Survey"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="date_of_survey"
                        :max="new Date().toISOString().split('T')[0]"
                    />
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Client"
                        v-model="client">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Type of Survey"
                        v-model="type_of_survey">
                    </v-text-field>
                    <v-file-input
                        class="mt-3"
                        density="compact"
                        label="Lot Plan"
                        v-model="lot_plan"
                        accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.dwg"
                        prepend-icon=""
                        prepend-inner-icon="mdi-file-cad"
                        hide-details
                        multiple
                        clearable
                        variant="outlined"
                        :rules="[v => !v || v.length === 0 || v[0].size < 104857600 || 'File size should be less than 100 MB!']">
                    </v-file-input>
                    <!-- <div v-if="current_attachment_url && isEditMode === 'edit'" class="mt-2 text-sm text-gray-600">
                    Current File:
                        <v-btn
                            v-for="(fileUrl, index) in Array.isArray(current_attachment_url) ? current_attachment_url : [current_attachment_url]"
                            :key="index"
                            color="primary"
                            variant="text"
                            size="small"
                            class="!normal-case">
                        <v-icon start>mdi-file-cad</v-icon> {{ getFileName(fileUrl) }}
                        </v-btn>
                    </div> -->
                    <div v-if="current_attachment_url && isEditMode === 'edit'" class="mt-2 text-sm text-gray-600">
                        <div v-for="(fileUrl, index) in Array.isArray(current_attachment_url) ? current_attachment_url : [current_attachment_url]" :key="index" class="flex items-center space-x-2">
                            <v-icon color="blue">mdi-file-cad</v-icon>
                            <span class="ml-2">{{ getFileName(editing, fileUrl, index) }}</span>
                            <v-tooltip bottom>
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        @click="removeAttachment(fileUrl)"
                                        size="small"
                                        color="red"
                                        class="cursor-pointer"
                                        v-bind="props">
                                        mdi-close-circle
                                    </v-icon>
                                </template>
                                <span>Remove this file</span>
                            </v-tooltip>
                        </div>
                    </div>
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
                            @click="submit"
                            color="green darken-1"
                            text
                            :loading="isSubmitting"
                            :disabled="isSubmitting || !date_of_survey">
                            {{ isEditMode === 'add' ? 'Submit' : 'Update'}}
                        </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>

    <Snackbar ref="snackbar"></Snackbar>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import axios from 'axios';
import Snackbar from '../../Components/Snackbar.vue';
import { usePage } from '@inertiajs/vue3'

const snackbar = ref(null)

const headers = ref([
    { title: 'Date of Survey', value: 'date_of_survey', align: 'center', sortable: true},
    { title: 'Client', value: 'client', align: 'center'},
    { title: 'Type of Survey', value: 'type_of_survey', align: 'center'},
    { title: 'Attachment', value: 'plan_attachment', align: 'center'},
    { title: 'Actions', value: 'actions', align: 'center'},
])

const plans = ref([])
const date_of_survey = ref('')
const client = ref('')
const type_of_survey = ref('')
const lot_plan = ref([])
const search = ref('')
const dialog = ref(false)
const isEditMode = ref('add')
const editing = ref(null)
const current_attachment_url = ref(null)
const isSubmitting = ref(false)
const filesToDelete = ref([])

const submit = async () => {
    isSubmitting.value = true
    const formData = new FormData()
    formData.append('client', client.value)
    formData.append('type_of_survey', type_of_survey.value)

    const formattedDate = formatDateForBackend(date_of_survey.value)
    if(formattedDate){
        formData.append('date_of_survey', formattedDate)
    }

    //append the list of files to be deleted
    if(filesToDelete.value.length > 0){
        formData.append('files_to_delete', JSON.stringify(filesToDelete.value));
    }

    //check if the lot_plan array contains any files
    if(lot_plan.value && lot_plan.value.length > 0){
        lot_plan.value.forEach(file => {
            if(file instanceof File){
                //correctly append each file with array notation
                formData.append('lot_plan_files[]', file)
            }
        })
    } 
    else if(isEditMode.value === 'add'){
        //if in 'add' mode and no file is selected, show a warning and return.
        //a file is required for new entries.
        snackbar.value.alertCustom('No file selected. A Lot Plan file is required.')
        isSubmitting.value = false
        return
    }
    
    try{
        let res;
        if(isEditMode.value === 'add'){
        res = await axios.post('/insert_survey_title', formData, {
            headers: {
            'Content-Type': 'multipart/form-data'
            }
        });
        }
        else{
        res = await axios.post(`/update_survey_title/${editing.value.id}`, formData, {
            headers: {
            'Content-Type': 'multipart/form-data',
            'X-HTTP-Method-Override': 'PUT'
            }
        });
        }

        if(res.data.success){
            if(isEditMode.value === 'add'){
                snackbar.value.alertSuccess(res.data.message)
            }
            else{
                snackbar.value.alertUpdate()
            }
            closeDialog()
            await fetchSurveyTitles()
        }
        else{
            if(res.data.errors){
                const errorMessages = Object.values(res.data.errors).flat().join('\n');
                snackbar.value.alertWarning(`Validation failed. Please check your inputs:\n${errorMessages}`)
            }
            else{
                snackbar.value.alertError(res.data.message)
            }
        }
    }
    catch(err){
        if(err.response && err.response.data && err.response.data.errors){
            const errorMessages = Object.values(err.response.data.errors).flat().join('\n')
            snackbar.value.alertWarning(`Validation failed. Please check your inputs:\n${errorMessages}`)
        }
        else{
        snackbar.value.alertWarning('An error occurred. Please try again.')
        console.error('Error submitting data:', err)
        }
    }
    finally {
        isSubmitting.value = false
    }
} //NOTE - end of submit & update function

const removeAttachment = (fileUrl) => {
    if(!current_attachment_url.value){
        current_attachment_url.value = []
    }
    // add the URL of the file to be deleted to our list, avoiding duplicates
    if(!filesToDelete.value.includes(fileUrl)){
        filesToDelete.value.push(fileUrl)
    }
    // normalize to array then filter out
    const urls = Array.isArray(current_attachment_url.value)
        ? current_attachment_url.value
        : [current_attachment_url.value]
    current_attachment_url.value = urls.filter(url => url !== fileUrl)
}

const closeDialog = () => {
    dialog.value = false
    date_of_survey.value = ''
    client.value = ''
    type_of_survey.value = ''
    lot_plan.value = []
    current_attachment_url.value = []
    editing.value = null
    isEditMode.value = 'add'
} //NOTE - end of close dialog

const openDialog = (mode, item = null) => {
    isEditMode.value = mode
    dialog.value = true
    date_of_survey.value = ''
    client.value = ''
    type_of_survey.value = ''
    lot_plan.value = []
    current_attachment_url.value = null
    filesToDelete.value = []

    if(mode === 'edit' && item){
        editing.value = item
        date_of_survey.value = item.date_of_survey
        client.value = item.client
        type_of_survey.value = item.type_of_survey
        current_attachment_url.value = Array.isArray(item.plan_attachment)
            ? [...item.plan_attachment]
            : (item.plan_attachment ? [item.plan_attachment] : [])
    }
    else{
        editing.value = null
    }
} //NOTE - end of open dialog


const formatDateForBackend = (dateValue) => { //date helper
    if(!dateValue) return null

    //if the dateValue is already a Date object, use it directly
    const d = dateValue instanceof Date ? dateValue : new Date(dateValue)

    //check if the date is valid
    if(isNaN(d.getTime())) return null

    //use the local date components to avoid timezone shifts
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')

    return `${year}-${month}-${day}`
}

// NEW FUNCTION: For simply extracting the filename from a URL string (used in the dialog)
const getUrlFileName = (url) => {
    if(typeof url === 'string'){
        return url.split('/').pop() || 'document.dwg';
    }
    return 'N/A';
}
// const getFileName = (url) => {
//     if(typeof url === 'string'){
//         return url.split('/').pop() || 'document.dwg'
//     }
//     return 'N/A'
// }
// MODIFIED FUNCTION: Prioritizes the saved original name (used in the data table)
const getFileName = (item, fileUrl, index = 0) => {
    const originalNameData = item.lot_plan_original_name;

    if(!originalNameData){
        //no original name stored, fall back to URL name
        return getUrlFileName(fileUrl);
    }
    
    //check for single-string (legacy/old data)
    if(typeof originalNameData === 'string'){
        //check if it's a simple string (starts with a character, not a bracket)
        if(originalNameData.trim().charAt(0) !== '['){
            return originalNameData;
        }
    }

    //attempt to parse as JSON array (new data)
    try{
        const originalNamesArray = (typeof originalNameData === 'string') 
            ? JSON.parse(originalNameData) 
            : originalNameData; //already an array/object if passed from fetchTitles

        if(Array.isArray(originalNamesArray) && originalNamesArray.length > index){
            return originalNamesArray[index];
        }
    }
    catch(e){
        //if parsing fails, but it was a string, return the string itself 
        //(as a final attempt for badly formatted legacy data)
        if (typeof originalNameData === 'string') {
            return originalNameData;
        }
        console.error("Error parsing lot_plan_original_name:", e);
    }
    
    //extract file name from the URL
    return getUrlFileName(fileUrl);
}

const userIsViewer = computed(() => {
    const authUser = usePage().props.auth?.user
    if (!authUser) return false
    const userEmpCode = authUser.emp_code
    const checkerEmpCode = '0001-0000'
    return userEmpCode === checkerEmpCode
})

const fetchSurveyTitles = async () => {
    try{
        const res = await axios.get('get_survey_title_data')
        plans.value = res.data.map(item => ({
            ...item,
            //ensure lot_plan_original_name is an array if it's not present or null
            //for consistency when used in getFileName.
            lot_plan_original_name: item.lot_plan_original_name 
                ? (typeof item.lot_plan_original_name === 'string' 
                    ? item.lot_plan_original_name //keep as string for now, updated by PHP
                    : item.lot_plan_original_name)
                : [],
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

onMounted(() => {
    fetchSurveyTitles()
})
</script>
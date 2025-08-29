<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
            <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">Survey Quotations</h1>
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
            :headers="headers"
            :items="quotations"
            :search="search">
            <template v-slot:item.pdf_attachment="{ item }">
                <div v-if="item.attachment_url" class="pdf-attachment-wrapper">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                color="primary"
                                variant="text"
                                size="large"
                                class="!normal-case"
                                @click="openPdfViewer(item.attachment_url)">
                            <v-icon start color="red">mdi-file-pdf-box</v-icon>
                            <span class="ml-2">{{ getFileNameVehicle(item.attachment_url) }}</span>
                            </v-btn>
                        </template>
                        <span>Click to view PDF</span>
                    </v-tooltip>
                </div>
                <span v-else class="text-grey">No attachment</span>
            </template>
            <template v-slot:item.pdf_revised_attachment="{ item }">
                <div v-if="item.revised_attachment_url" class="pdf-attachment-wrapper">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                color="primary"
                                variant="text"
                                size="large"
                                class="!normal-case"
                                @click="openPdfViewer(item.revised_attachment_url)">
                            <v-icon start color="red">mdi-file-pdf-box</v-icon>
                            <span class="ml-2">{{ getFileNameVehicle(item.revised_attachment_url) }}</span>
                            </v-btn>
                        </template>
                        <span>Click to view PDF</span>
                    </v-tooltip>
                </div>
                <span v-else class="text-grey">No attachment</span>
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
                                        <!-- NOTE - start of add & edit dialog -->
        <v-dialog class="transition-discrete md:transition-normal" persistent v-model="dialog" width="450px" no-click-animation>
            <v-card>
                <v-card-title 
                    style="background: linear-gradient(135deg, #0047AB, #50C878); 
                    color: white;"> {{ isEditMode === 'add' ? 'Add' : 'Edit' }}
                </v-card-title>
                <v-card-text>
                    <v-text-field
                        hide-details
                        label="Client"
                        v-model="client">
                    </v-text-field>
                    <v-text-field
                        class="mt-3"
                        hide-details
                        label="Location"
                        v-model="location">
                    </v-text-field>
                    <v-date-input
                        class="mt-3"
                        density="compact"
                        hide-details
                        label="Date"
                        prepend-icon=""
                        prepend-inner-icon="mdi-calendar"
                        variant="outlined"
                        v-model="date"
                        :max="new Date().toISOString().split('T')[0]"
                    />
                    <v-file-input
                        class="mt-3"
                        density="compact"
                        label="PDF Attachment"
                        v-model="attachment"
                        accept=".pdf"
                        prepend-icon=""
                        prepend-inner-icon="mdi-file-pdf-box"
                        hide-details
                        clearable
                        variant="outlined"
                        :rules="[v => !v || v.length === 0 || v[0].size < 10485760 || 'PDF size should be less than 10 MB!']">
                    </v-file-input>
                    <div v-if="current_attachment_url && isEditMode === 'edit'" class="mt-2 text-sm text-gray-600">
                        Current PDF:
                        <v-btn
                            color="primary"
                            variant="text"
                            size="small"
                            class="!normal-case"
                            @click="openPdfViewerVehicle(current_attachment_url)">
                            <v-icon start>mdi-file-pdf-box</v-icon> View {{ getFileNameVehicle(current_attachment_url) }}
                        </v-btn>
                    </div>
                    <v-file-input
                        class="mt-3"
                        density="compact"
                        label="Revision"
                        v-model="revised_attachment"
                        accept=".pdf"
                        prepend-icon=""
                        prepend-inner-icon="mdi-file-pdf-box"
                        hide-details
                        clearable
                        variant="outlined"
                        :rules="[v => !v || v.length === 0 || v[0].size < 10485760 || 'PDF size should be less than 10 MB!']">
                    </v-file-input>
                    <div v-if="current_revised_attachment_url && isEditMode === 'edit'" class="mt-2 text-sm text-gray-600">
                        Current PDF:
                        <v-btn
                            color="primary"
                            variant="text"
                            size="small"
                            class="!normal-case"
                            @click="openPdfViewerVehicle(current_revised_attachment_url)">
                            <v-icon start>mdi-file-pdf-box</v-icon> View {{ getFileNameVehicle(current_revised_attachment_url) }}
                        </v-btn>
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
                            text>
                            {{ isEditMode === 'add' ? 'Submit' : 'Update' }}
                        </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
                                        <!-- NOTE - pdf viewer dialog -->
        <PdfViewerDialog 
            :dialog="pdfViewerDialog" 
            :pdf-url="pdfToViewUrl" 
            @update:dialog="pdfViewerDialog = $event">
        </PdfViewerDialog>
        <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import PdfViewerDialog from '../../Components/PdfViewerDialog.vue';
import Snackbar from '../../Components/Snackbar.vue';

const snackbar = ref(null)

const headers = ref([
    { title:  'Client', value: 'client', align: 'center'},
    { title:  'Location', value: 'location', align: 'center'},
    { title:  'Date', value: 'date', align: 'center'},
    { title:  'Attachment', value: 'pdf_attachment', align: 'center'},
    { title:  'Revision', value: 'pdf_revised_attachment', align: 'center'},
    { title:  'Actions', value: 'actions', align: 'center'},
])

const quotations = ref([])
const search = ref('')
const client = ref('')
const location = ref('')
const date = ref('')
const attachment = ref('')
const revised_attachment = ref('')

const dialog = ref(false)
const isEditMode = ref('add')
const current_attachment_url = ref(null)
const current_revised_attachment_url = ref(null)
const editing = ref(null)

const pdfViewerDialog = ref(false)
const pdfToViewUrl = ref(null)

const openPdfViewer = (url) => {
    pdfToViewUrl.value = url
    pdfViewerDialog.value = true
}

const submit = async () => {
    const formData = new FormData()
    formData.append('client', client.value)
    formData.append('location', location.value)
    const formattedDate = formatDateForBackend(date.value);
    if (formattedDate) {
        formData.append('date', formattedDate);
    }

    const attToAppend = attachment.value instanceof File ? attachment.value : (attachment.value?.[0] instanceof File ? attachment.value[0]: null)

    const revAttToAppend = revised_attachment.value instanceof File ? revised_attachment.value : (revised_attachment.value?.[0] instanceof File ? revised_attachment.value[0]: null)

    if(attToAppend){
    formData.append('attachment_file', attToAppend);
    console.log('Appending Att. PDF file:', attToAppend.name)
    }
    else{
        console.log('No primary PDF file selected or file object is invalid')
    }

    if(revAttToAppend){
        formData.append('revised_attachment_file', revAttToAppend)
        console.log('Appending Revised Att. PDF file:', revAttToAppend.name)
    }
    else{
        console.log('No revised PDF file selected or file object is invalid')
    }

    try{
        let res
        if(isEditMode.value === 'add'){
            res = await axios.post('insert_survey_quotation', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            })
        }
        else{
            res = await axios.post(`update_survey_quotation/${editing.value.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PUT' //alternative to _method
                }
            })
        }
        if(res.data.success){
            if(isEditMode.value === 'add'){
                snackbar.value.alertSuccess(res.data.message)
            } else {
                snackbar.value.alertUpdate()
            }
            closeDialog()
            // Refresh the data after successful operation
            await fetchSurveyQuotations()
        }
        else{
            if(res.data.errors){
                const errorMessages = Object.values(res.data.errors).flat().join('\n')
                snackbar.value.alertWarning(`Validation failed:\n${errorMessages}`)
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
            snackbar.value.alertWarning('An error occurred. Please try again later.')
            console.error('Error submitting data:', err)
        }
    }
} //NOTE - end of submit & update function

const closeDialog = () => {
    dialog.value = false
    client.value = ''
    location.value = ''
    date.value = ''
    attachment.value = null
    current_attachment_url.value = null
    revised_attachment.value = null
    current_revised_attachment_url.value = null
    editing.value = null
    isEditMode.value = 'add'
} //NOTE - end of close dialog

const openDialog = (mode, item = null) => {
    isEditMode.value = mode
    dialog.value = true
    attachment.value = null
    revised_attachment.value = null

    if(mode === 'edit' && item){
        console.log(item)
        editing.value = item
        client.value = item.client
        location.value = item.location
        date.value = item.date
        current_attachment_url.value = item.attachment_url
        current_revised_attachment_url.value = item.revised_attachment_url
    }
    else{
        editing.value = null
        client.value = ''
        location.value = ''
        date.value = ''
        current_attachment_url.value = ''
        current_revised_attachment_url.value = ''
    }
} //NOTE - end of open dialog


const formatDateForBackend = (dateValue) => { //helper function to format date
    if(!dateValue) return null;
    
    if(typeof dateValue === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateValue)){
        return dateValue;
    }
    
    const d = new Date(dateValue);
    if(isNaN(d.getTime())) return null;
    
    return d.toISOString().split('T')[0];
};

const getFileNameVehicle = (url) => {
    return url.split('/').pop() || 'document.pdf'
}

const fetchSurveyQuotations = async () => {
    try{
        const res = await axios.get('get_quotations')
        quotations.value = res.data.map(item => ({
            ...item
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

onMounted(() => {
    fetchSurveyQuotations()
})
</script>

<style scoped>
.pdf-attachment-wrapper{
    display: inline-flex;
    align-items: center;
}
</style>
<template>
    <h1 class="p-4 font-sans indent-8 text-2xl">LIST(S) OF SURVEY</h1>

    <SurveyTable
        :headers="headers"
        :survey_data="surveyData"
        @refresh-data="fetchSurveyData"
        @open-dialog="openAddDialog"
        @edit-data="openEditDialog">
    </SurveyTable>

    <v-dialog class="transition-discrete md:transition-normal" v-model="dialog" no-click-animation persistent width="700">
        <v-card>
            <v-card-title 
                style="background: linear-gradient(135deg, #0047AB, #50C878); 
                color: white;">
                {{ isEditMode ? 'Edit' : 'Add' }}
            </v-card-title>
            <v-card-text>
                <v-date-input
                    density="compact"
                    hide-details
                    label="Date Started"
                    prepend-icon=""
                    prepend-inner-icon="mdi-calendar"
                    variant="outlined"
                    v-model="tempData.date_started"
                />
                <v-date-input
                    class="mt-3"
                    density="compact"
                    hide-details
                    label="Date Completed"
                    prepend-icon=""
                    prepend-inner-icon="mdi-calendar"
                    variant="outlined"
                    v-model="tempData.date_completed"
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
                <div style="display: flex; align-items: center;">
                    <v-checkbox
                        color="primary"
                        direction="horizontal" 
                        density="compact" 
                        hide-details 
                        v-model="tempData.survey"
                        label="Survey">
                    </v-checkbox>
                    <v-checkbox
                        class="ms-8"
                        color="primary"
                        direction="horizontal" 
                        density="compact" 
                        hide-details 
                        v-model="tempData.data_process"
                        label="Data">
                    </v-checkbox>
                    <v-checkbox
                        class="ms-8"
                        color="primary"
                        direction="horizontal" 
                        density="compact" 
                        hide-details 
                        v-model="tempData.plans"
                        label="Plans">
                    </v-checkbox>
                </div>
                <v-text-field
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
                    label="Date Delivered"
                    prepend-icon=""
                    prepend-inner-icon="mdi-calendar"
                    variant="outlined"
                    v-model="tempData.date_delivered"
                />
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
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SurveyTable from '../Components/SurveyTable.vue';
import Snackbar from '../Components/Snackbar.vue';

const headers = ref([
    { title: 'Date Started', value: 'date_started', align: 'center' },
    { title: 'Date Completed', value: 'date_completed', align: 'center' },
    { title: 'Location', value: 'location', align: 'center' },
    { title: 'Type of Survey', value: 'survey_details', align: 'center' },
    { title: 'Area', value: 'area', align: 'center' },
    { title: 'Processed By', value: 'processed_by' },
    { title: 'Survey Progress Tracker', align: 'center', children: [
        { title: 'Survey', value: 'survey', align: 'center' },
        { title: 'Data Process', value: 'data_process', align: 'center' },
        { title: 'Plans', value: 'plans', align: 'center' },
    ]},
    { title: 'Remarks', value: 'remarks', align: 'center' }
])

const snackbar = ref(null)

const surveyData = ref([])
const tempData = ref({})
const edit = ref({})
const dialog = ref(false)
const isEditMode = ref(false)

function formatDate(dateString) {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
        console.error('Invalid date:', dateString);
        return null;
    }
    return date.toISOString().split('T')[0]
}

// async function submitForm() {
//     try{
//         tempData.value.date_started = formatDate(tempData.value.date_started)
//         tempData.value.date_completed = formatDate(tempData.value.date_completed)
//         tempData.value.date_approved = formatDate(tempData.value.date_approved)
//         tempData.value.date_delivered = formatDate(tempData.value.date_delivered)

//         if(!isEditMode.value){
//             const res = await axios.post('insert_survey_data', tempData.value)
//             console.log(res.data.message)
//             snackbar.value.alertSuccess()
//             closeAddDialog()
//             tempData.value = {}
//             fetchSurveyData()
//         } 
//         else{
//             const res = await axios.post('update_survey_data', tempData.value)
//             console.log(res.data.message)
//             snackbar.value.alertUpdate()
//             closeAddDialog()
//             tempData.value = {}
//             fetchSurveyData()
//         }

//     }
//     catch(error){
//         if(error.response && error.response.data.errors){
//             console.log(error.response.data.errors)
//         }
//         else{
//             console.error('An error occurred:', error)
//         }
//     }
// }

const submitForm = async () => {
    // console.log(tempData.value.date_completed, 'tempData.value.date_completed')

    const to_update = tempData.value

    if(!isEditMode.value){
        const dateStarted = new Date(tempData.value.date_started)
        const dateCompleted = tempData.value.date_completed ? new Date(tempData.value.date_completed) : null

        dateStarted ? dateStarted.setDate(dateStarted.getDate() + 1) : null
        dateCompleted ? dateCompleted.setDate(dateCompleted.getDate() + 1) : null

        tempData.value.date_started = formatDate(dateStarted)
        tempData.value.remarks = tempData.value.remarks

        tempData.value.date_completed = dateCompleted ? formatDate(dateCompleted) : null

        tempData.value.date_approved = tempData.value.date_approved ? formatDate(tempData.value.date_approved) : null
        tempData.value.date_delivered = tempData.value.date_delivered ? formatDate(tempData.value.date_delivered) : null

        axios({
            method: 'post',
            url: 'insert_survey_data',
            data: {
                to_update
            }
        }).then((res) =>{
            console.log(res.data)
            fetchSurveyData()
            snackbar.value.alertSuccess()
            dialog.value = false
            tempData.value = {}
        }).catch(() => {
            snackbar.value.alertError()
        })
    }
    else{
        const dateCompleted = tempData.value.date_completed ? new Date(tempData.value.date_completed) : null
        dateCompleted ? dateCompleted.setDate(dateCompleted.getDate() + 1) : null

        // tempData.value.date_started = formatDate(dateStarted)
        tempData.value.remarks = tempData.value.remarks

        tempData.value.date_completed = dateCompleted ? formatDate(dateCompleted) : null
        tempData.value.date_approved = tempData.value.date_approved ? formatDate(tempData.value.date_approved) : null
        tempData.value.date_delivered = tempData.value.date_delivered ? formatDate(tempData.value.date_delivered) : null

        axios({
            method: 'post',
            url: 'update_survey_data',
            data: {
                to_update
            }
        }).then((res) => {
            console.log(res.data)
            fetchSurveyData()
            snackbar.value.alertUpdate()
            dialog.value = false
            tempData.value = {}
        }).catch(() => {
            snackbarMessage.value = 'There was an error updating your data. Please try again.';
            snackbar.value = true
        })
    }
}


const openEditDialog = (item) => {
    fetchSurveyData()
    tempData.value = item
    isEditMode.value = true
    dialog.value = true
    edit.value = { ...item }
    tempData.value.survey = item.survey == 1 ? true : false
    tempData.value.data_process = item.data_process == 1 ? true : false
    tempData.value.plans = item.plans == 1 ? true : false
}

const openAddDialog = () => {
    isEditMode.value = false
    dialog.value = true
}

const closeAddDialog = () => {
    tempData.value = {}
    dialog.value = false
    fetchSurveyData()
}

const fetchSurveyData = async () => {
    try{
        const res = await axios.get('/get_survey_data')
        surveyData.value = res.data
        console.log(res.data);
    }
    catch(error){
        console.error('Error fetching Survey data', error);
        
    }
}

onMounted(() => {
    fetchSurveyData()
});
</script>

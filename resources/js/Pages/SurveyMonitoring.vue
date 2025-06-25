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
                    </v-checkbox>
                </div>                
                <v-date-input
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
                    hide-details
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
    { title: 'Location', value: 'location', align: 'center' },
    { title: 'Type of Survey', value: 'survey_details', align: 'center' },
    { title: 'Area', value: 'area', align: 'center' },
    { title: 'Processed By', value: 'processed_by' },
    { title: 'Survey', value: 'survey', align: 'center' },
    { title: 'Making of Plans (Data Process)', align: 'center', children: [
        { title: 'Start', value: 'data_process', align: 'center' },
        { title: 'End', value: 'plans', align: 'center' },
    ]},
    { title: 'Date Approved', value: 'date_approved', align: 'center' },
    { title: 'Date Completed/Delivered', value: 'date_completed', align: 'center' },
    { title: 'Remarks', value: 'remarks', align: 'center' }
])

const snackbar = ref(null)

const surveyData = ref([])
const tempData = ref({})
const edit = ref({})
const dialog = ref(false)
const isEditMode = ref(false)
const currentUser = ref(null)

const fetchCurrentUser = async () => {
    try{
        const res = await axios.get('/get_current_user')
        currentUser.value = res.data
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
    to_update.data_process = !!to_update.data_process ? 1 : 0;
    to_update.plans = !!to_update.plans ? 1 : 0;

    // Format dates only if they are not already formatted
    const dateFields = ['date_started', 'date_completed', 'date_approved', 'date_delivered'];
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

    const url = isEditMode.value ? 'update_survey_data' : 'insert_survey_data';

    axios({
        method: 'post',
        url,
        data: { to_update }
    }).then((res) => {
        fetchSurveyData();
        if (isEditMode.value) {
            snackbar.value.alertUpdate();
        } else {
            snackbar.value.alertSuccess();
        }
        dialog.value = false;
        tempData.value = {};
    }).catch(() => {
        snackbar.value.alertError();
    });
};

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

    if (currentUser.value){
        const excludedUserIds = [1, 2, 4, 12]

        if(excludedUserIds.includes(currentUser.value.id)){
            tempData.value.processed_by = currentUser.value.username
        } 
        else{
            tempData.value.processed_by = `Engr. ${currentUser.value.username}`
        }
    }
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
    fetchCurrentUser()
});
</script>

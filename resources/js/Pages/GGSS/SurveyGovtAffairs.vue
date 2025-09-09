<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
            <h1 class="p-2 sm:p-4 font-sans sm:indent-8 text-xl sm:text-2xl">Government Related (External Affairs)</h1>
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
                                    <!-- NOTE - start of equipment table -->
        <v-data-table
            class="font-sans"
            density="compact"
            fixed-footer
            fixed-header
            item-key="id"
            :headers="headers"
            :items="items"
            :search="search">
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
        <v-dialog   class="transition-discrete md:transition-normal" v-model="dialog" no-click-animation persistent width="500">
            <v-card>
                <v-card-title 
                    style="background: linear-gradient(135deg, #0047AB, #50C878); 
                    color: white;">
                    {{ isEditMode ? 'Edit' : 'Add'}}
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
                            label="Client"
                            v-model="tempData.client">
                        </v-text-field>
                        <v-text-field
                            class="mt-3"
                            hide-details
                            label="Location"
                            v-model="tempData.location">
                        </v-text-field>
                        <v-text-field
                            class="mt-3"
                            hide-details
                            label="Type of Survey/Plan"
                            v-model="tempData.type_of_plan_survey">
                        </v-text-field>
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
                            label="Duration"
                            v-model="tempData.duration">
                        </v-text-field>
                        <v-text-field
                            class="mt-3"
                            hide-details
                            label="Remarks"
                            v-model="tempData.remarks">
                        </v-text-field>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer/>
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
                        {{ isEditMode ? 'Update' : 'Submit' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import Snackbar from '../../Components/Snackbar.vue';
import axios from 'axios';

const snackbar = ref(null)

const headers = ref([
    { title: 'Date Started', value: 'date_started', align: 'center'},
    { title: 'Client', value: 'client', align: 'center'},
    { title: 'Location', value: 'location', align: 'center'},
    { title: 'Type of Plan/Survey', value: 'type_of_plan_survey', align: 'center'},
    { title: 'Date Completed', value: 'date_completed', align: 'center'},
    { title: 'Duration', value: 'duration', align: 'center'},
    { title: 'Remarks', value: 'remarks', align: 'center'},
    { title: 'Actions', value: 'actions', align: 'center'},
])
const items = ref([])
const search = ref('')
const currentUser = ref(null)
const tempData = ref({})
const dialog = ref(false)
const isEditMode = ref(false)
const edit = ref({})

const submitForm = async () => {
    const to_update = { ...tempData.value }

    //calculate duration before sending to backend
    if(to_update.start_actual && to_update.date_completed){
        const startDate = new Date(to_update.start_actual)
        const endDate = new Date(to_update.date_completed)
        if(!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())){
            const diffTime = endDate - endDate
            const diffDays = Math.max(Math.round(diffTime / (1000 * 60 * 60 * 24)), 0)
            to_update.duration = diffDays
        } 
        else{
            to_update.duration = null
        }
    }
    else{
        to_update.duration = null
    }

    //format date only if they are not already formatted
    const dateFields = ['date_started', 'date_completed']
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

    const url = isEditMode.value ? 'update_survey_govt_related_data' : 'insert_survey_govt_related_data'

    axios({
        method: 'post',
        url,
        data: { to_update }
    }).then(() => {
        fetchSurveyGovtRelated()
        if(isEditMode.value){
            snackbar.value.alertUpdate()
        }
        else{
            snackbar.value.alertSuccess()
        }
        fetchSurveyGovtRelated()
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

const closeAddDialog = () => {
    tempData.value = {}
    dialog.value = false
    fetchSurveyGovtRelated()
}

const openDialog = (mode, item = null) => {
    const data = { ...item }
    isEditMode.value = (mode === 'edit')
    if(mode === 'add'){
        resetForm()
        dialog.value = true
        
    }
    else if(mode === 'edit' && item){
        //format date fields for the date input
        ['date_started', 'date_completed'].forEach(field => {
            if (data[field]) {
                const d = new Date(data[field])
                if(!isNaN(d.getTime())){
                    const year = d.getFullYear()
                    const month = String(d.getMonth() + 1).padStart(2, '0')
                    const day = String(d.getDate()).padStart(2, '0')
                    data[field] = `${year}-${month}-${day}`
                } 
                else{
                    data[field] = ''
                }
            }
        })
        tempData.value = data
        dialog.value = true
    }
}

const resetForm = () => {
    tempData.value = {}
    edit.value = null
    isEditMode.value = false
}

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

const fetchSurveyGovtRelated = async () => {
    try{
        const res = await axios.get('/get_survey_govt_related_data')
        items.value = res.data
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

watch(() => [tempData.value.date_started, tempData.value.date_completed],
    ([start, end]) => {
        if(start && end){
        const startDate = new Date(start)
        const endDate = new Date(end)
            if(!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())){
                //calculate the difference in days
                const diffTime = endDate - startDate
                const diffDays = Math.max(Math.round(diffTime / (1000 * 60 * 60 * 24)), 0)
                tempData.value.duration = diffDays
            } 
            else{
                tempData.value.duration = ''
            }
        } 
        else{
        tempData.value.duration = ''
        }
    }
)

onMounted(() => {
    fetchSurveyGovtRelated()
    fetchCurrentUser()
})
</script>
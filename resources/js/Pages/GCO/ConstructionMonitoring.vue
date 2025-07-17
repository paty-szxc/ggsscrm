<template>
    <ConstructionTaskTable
        :headers="headers"
        :construction_projects_data="constructionData"
        @refresh-data="fetchConstructionsData"
        @open-dialog="openAddDialog"
        @edit-data="openEditDialog">
    </ConstructionTaskTable>

    <v-dialog class="transition-discrete md:transition-normal" v-model="dialog" no-click-animation persistent width="500">
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
                    label="Particulars"
                    v-model="tempData.particulars">
                </v-text-field>
                <v-text-field
                    class="mt-3"
                    hide-details
                    label="Processed By"
                    v-model="tempData.processed_by">
                </v-text-field>
                <div style="display: flex; align-items: center;" class="gap-3">
                    <span class="m-2">Processing of Permits</span>
                    <v-checkbox
                        color="primary"
                        direction="horizontal" 
                        density="compact" 
                        hide-details 
                        v-model="tempData.start_process"
                        label="Start">
                    </v-checkbox>
                    <v-checkbox
                        color="primary"
                        direction="horizontal" 
                        density="compact" 
                        hide-details 
                        v-model="tempData.end_process"
                        label="End">
                    </v-checkbox>
                </div>
                <div style="display: flex; align-items: center;" class="gap-3">
                    <span class="m-2">Actual Construction</span>
                    <v-checkbox
                        color="primary"
                        direction="horizontal" 
                        density="compact" 
                        hide-details 
                        v-model="tempData.start_actual"
                        label="Start">
                    </v-checkbox>
                    <v-checkbox
                        color="primary"
                        direction="horizontal" 
                        density="compact" 
                        hide-details 
                        v-model="tempData.end_actual"
                        label="End">
                    </v-checkbox>
                </div>
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
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import axios from 'axios';
import ConstructionTaskTable from '../../Components/ConstructionTaskTable.vue';
import Snackbar from '../../Components/Snackbar.vue';

const headers = ref([
    { title: 'Date Started', value: 'date_started', align: 'center' },
    { title: 'Location', value: 'location', align: 'center' },
    { title: 'Particulars', value: 'particulars', align: 'center' },
    { title: 'Processed By', value: 'processed_by', align: 'center' },
    { title: 'Processing of Permits', align: 'center', children: [
        { title: 'Start', value: 'start_process', align: 'center' },
        { title: 'End', value: 'end_process', align: 'center' },
    ]},
    { title: 'Actual Construction', align: 'center', children: [
        { title: 'Start', value: 'start_actual', align: 'center' },
        { title: 'End', value: 'end_actual', align: 'center' },
    ]},
    { title: 'Date Completed/Delivered', value: 'date_completed', align: 'center' },
])

const currentUser = ref(null)
const constructionData = ref([])
const tempData = ref({})
const dialog = ref(false)
const edit = ref({})
const isEditMode = ref(false)
const snackbar = ref(null)

const submitForm = async () => {
    const to_update = { ...tempData.value }
    const dateFields = ['date_started', 'date_completed']
    dateFields.forEach(field => {
        if (to_update[field]) {
            //if it's already in YYYY-MM-DD format, keep it as is
            if (typeof to_update[field] === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(to_update[field])) {
                //already formatted correctly, no need to change
                return
            }
            
            //if it's a Date object or other format, convert it properly
            const d = new Date(to_update[field])
            if (!isNaN(d.getTime())) {
                //use local date to avoid timezone issues
                const year = d.getFullYear()
                const month = String(d.getMonth() + 1).padStart(2, '0')
                const day = String(d.getDate()).padStart(2, '0')
                to_update[field] = `${year}-${month}-${day}`
            } else {
                to_update[field] = null
            }
        }
    })

    const url = isEditMode.value ? 'update_construction_project_data' : 'insert_construction_project_data'
    axios({
        method: 'post',
        url,
        data: { to_update }
    }).then((res) => {
        fetchConstructionsData()
        if(isEditMode.value){
            snackbar.value.alertUpdate()
            console.log(res.data)
            
        }
        else{
            snackbar.value.alertSuccess()
            console.log(res.data)
        }
        dialog.value = false
        tempData.value = {}
    }).catch(() => {
        snackbar.value.alertError()
    })
}

const openEditDialog = (item) => {
    //clone the item to avoid mutating the original
    const data = { ...item };

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
    isEditMode.value = true
    dialog.value = true
    tempData.value.start_process = item.start_process == 1 ? true : false
    tempData.value.end_process = item.end_process == 1 ? true : false
    tempData.value.start_actual = item.start_actual == 1 ? true : false
    tempData.value.end_actual = item.end_actual == 1 ? true : false
    edit.value = { ...item }
}

const closeAddDialog = () => {
    tempData.value = {}
    dialog.value = false
    fetchConstructionsData()
}

const openAddDialog = () => {
    isEditMode.value = false
    dialog.value = true

    if(currentUser.value){
        const excludedUserIds = [2, 4]

        if(excludedUserIds.includes(currentUser.value.id)){
            tempData.value.processed_by = currentUser.value.username
        } 
        else{
            tempData.value.processed_by = `Engr. ${currentUser.value.username}`
        }
    }
}

const fetchCurrentUser = async () => {
    try{
        const res = await axios.get('/get_current_user')
        currentUser.value = res.data
        console.log(res.data)
        
    } 
    catch(error){
        console.error('Error fetching user data', error)
    }
}

const fetchConstructionsData = async () => {
    try{
        const res = await axios.get('/get_construction_project_data')
        constructionData.value = res.data
        console.log(res.data)
    }
    catch(error){
        console.error('Error fetching Survey data', error)
        
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
    fetchConstructionsData()
    fetchCurrentUser()
})
</script>

<style scoped>
.checkbox-row {
    display: flex;
    align-items: center;
    gap: 8px; /* Adjust spacing as needed */
}
.checkbox-inline {
    margin: 0 8px 0 0 !important; /* Remove default margin and add right spacing */
    min-width: 0; /* Prevents extra width */
}
</style>
<template>
    <v-container fluid>
        <div class="flex flex-wrap gap-4 items-center mb-4">
            <div class="flex-grow-1 min-w-[250px]">
                <v-file-input
                    accept=".xlsx,.xls,.csv"
                    @change="onFileChange"
                    clearable
                    density="compact"
                    hide-details
                    prepend-inner-icon="mdi-paperclip"
                    prepend-icon=""
                    variant="outlined"
                    v-model="file">
                </v-file-input>
            </div>
            <div class="flex-shrink-0">
                <v-btn 
                    class="bg-green-500 text-white" 
                    @click="uploadFile"
                    :disabled="isLoading"
                    :loading="isLoading">
                    <template v-if="isLoading">
                        <div class="flex items-center space-x-2">
                            <div class="animate-bounce">📁</div>
                            <span>IMPORTING...</span>
                        </div>
                    </template>
                    <template v-else>
                        IMPORT FILE
                    </template>
                </v-btn>
            </div>
            <div class="flex-grow-1 min-w-[250px]">
                <v-text-field
                    clearable
                    hide-details
                    label="Search"
                    prepend-inner-icon="mdi-magnify"
                    v-model="search">
                </v-text-field>
            </div>
            <div class="flex-shrink-0">
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-fab
                            @click="addBtn()"
                            color="info"
                            icon
                            size="small"
                            v-bind="props">
                            <v-icon>mdi-plus</v-icon>
                        </v-fab>
                    </template>
                    <span>Add</span>
                </v-tooltip>
            </div>
        </div>
        <h1 class="p-4 font-sans indent-8 text-2xl">LIST(S) OF CONSTRUCTIONS</h1>
        <!-- <v-data-table
            class="font-sans"
            density="compact"
            fixed-footer
            fixed-header
            :headers="headers"
            :items="construction_projects_data"
            item-key="id"
            :search="search"
            @dblclick:row="(item, event) => editData(event, item)"
            v-model:items-per-page="itemsPerPage">
        </v-data-table> -->
        <v-data-table
            class="font-sans"
            density="compact"
            fixed-footer
            fixed-header
            :headers="headers"
            :items="construction_projects_data"
            item-key="id"
            :search="search"
            show-expand
            @dblclick:row="(item, event) => editData(event, item)"
            v-model:items-per-page="itemsPerPage">
            <template v-slot:item.permit_duration="{ item }">
                {{ item.permit_duration ? `${item.permit_duration} days` : '' }}
            </template>
            <template v-slot:item.construction_duration="{ item }">
                {{ item.construction_duration ? `${item.construction_duration} days` : '' }}
            </template>
            <template v-slot:item.data-table-expand="{ internalItem, isExpanded, toggleExpand }">
            <v-btn
                :append-icon="isExpanded(internalItem) ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                :text="isExpanded(internalItem) ? 'Collapse' : 'More info'"
                size="small"
                variant="outlined"
                slim
                @click="toggleExpand(internalItem)" >
                </v-btn>
            </template>
            <template v-slot:expanded-row="{ columns, item }">
                <tr>
                    <td :colspan="columns.length" class="py-2">
                    <v-sheet rounded="lg">
                        <v-table density="compact">
                            <thead>
                                <tr>
                                <!-- <th class="bg-inherit font-sans text-center">Date Completed/Delivered</th> -->
                                <th class="bg-inherit font-sans text-center">Overall Duration</th>
                                <th class="bg-inherit font-sans text-center">Contact Person</th>
                                <th class="bg-inherit font-sans text-center">Contact No.</th>
                                <th class="bg-inherit font-sans text-center">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <!-- <td class="bg-inherit font-sans text-center">{{ item.date_completed }}</td> -->
                                    <td class="bg-inherit font-sans text-center">{{ item.duration ? `${item.duration} days` : ''}}</td>
                                    <td class="bg-inherit font-sans text-center">{{ item.contact_person }}</td>
                                    <td class="bg-inherit font-sans text-center">{{ item.contact_no }}</td>
                                    <td class="bg-inherit font-sans text-center">{{ item.remarks }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-sheet>
                    </td>
                </tr>
            </template>
        </v-data-table>

        <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import axios from 'axios';
import { defineEmits, defineProps, ref } from 'vue';
import Snackbar from './Snackbar.vue';

const { headers, construction_projects_data } = defineProps({
    headers: Array,
    construction_projects_data: Array,
})

const search = ref('')
const itemsPerPage = ref(12)
const isLoading = ref(false)
const snackbar = ref(null)

const emit = defineEmits(['refresh-data', 'open-dialog', 'edit-data'])
const addBtn = () => {
    emit('open-dialog');
};

const editData = (item) => {
    emit('edit-data', item.item);
};
const file = ref(null)

const onFileChange = (event) => {
    file.value = event.target.files[0]
}

const uploadFile = async () => {
    if(!file.value) {
        alert('Please select a file to upload.')
        return
    }
    const formData = new FormData()
    formData.append('file', file.value)

    try{
        isLoading.value = true
        const response = await axios.post('/import_construction_projects_data', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        snackbar.value.alertImport()
        emit('refresh-data')
        file.value = {}
    } catch (error) {
        console.error('Error uploading file:', error)
        alert('Error uploading file. Please try again.')
    } finally {
        isLoading.value = false
    }
}
</script>
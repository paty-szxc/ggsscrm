<template>
    <v-container>
        <div class="flex justify-start mb-2 space-x-12">
            <v-file-input
                accept=".xlsx,.xls,.csv"
                @change="onFileChange"
                clearable
                density="compact"
                hide-details
                max-width="25%"
                prepend-inner-icon="mdi-paperclip"
                prepend-icon=""
                variant="outlined"
                v-model="file">
            </v-file-input>
            <v-btn class="bg-green-500 text-white" @click="uploadFile">IMPORT FILE</v-btn>
            <v-text-field
                hide-details
                label="Search"
                prepend-inner-icon="mdi-magnify"
                max-width="25%"
                v-model="search"
                >
            </v-text-field>

            <!-- <v-btn class="bg-blue-500 text-white"></v-btn> -->
            <div class="ml-auto">
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
        <template>
            <v-file-input
                density="compact" 
                hide-details
                variant="outlined" 
                style="width: 350px">
            </v-file-input>
        </template>
        <v-data-table
            class="font-sans"
            density="compact"
            fixed-footer
            fixed-header
            :headers="headers"
            :items="sales_revenue_data"
            item-key="id"
            :search="search"
            show-expand
            @dblclick:row="(item, event) => editData(event, item)"
            v-model:items-per-page="itemsPerPage">
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
                                <th class="bg-inherit font-sans text-center">Receivable Bal</th>
                                <th class="bg-inherit font-sans text-center">Withholding Tax.</th>
                                <th class="bg-inherit font-sans text-center">Fully Paid Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bg-inherit font-sans text-center">{{ item.receivable_bal }}</td>
                                    <td class="bg-inherit font-sans text-center">{{ item.withholding_tax }}</td>
                                    <td class="bg-inherit font-sans text-center">{{ item.fully_paid_date }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-sheet>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-container>
</template>

<script setup>
import axios from 'axios';
import { defineEmits, defineProps, ref } from 'vue';

const { headers, sales_revenue_data } = defineProps({
    headers: Array,
    sales_revenue_data: Array,
})

const search = ref('')
const itemsPerPage = ref(15)

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
        const response = await axios.post('/import_sales_revenue_data', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        alert(response.data.message || 'File imported successfully!')
        emit('refresh-data')
    }
    catch(error){
        console.error('Error uploading file:', error)
        alert('Error uploading file. Please try again.')
    }
}
</script>
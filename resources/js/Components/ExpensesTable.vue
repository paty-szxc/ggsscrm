<template>
    <div class="overflow-x-auto">
    <h3 class="text-lg font-semibold ml-4 ">{{ expensesType }} Vouchers Expenses</h3>
        <div class="flex justify-start mb-2 ml-3 space-x-12">
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
            <v-text-field
                clearable
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
        <v-data-table
            @dblclick:row="(item, event) => editData(event, item)"
            class="font-sans"
            density="compact"
            fixed-footer
            fixed-header
            :headers="voucher_headers"
            :items="vouchers"
            item-key="id"
            :search="search"
            show-expand
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
                                <th
                                    v-for="col in expandedColumns"
                                    :key="col.key"
                                    class="bg-inherit font-sans text-center"
                                >
                                    {{ col.title }}
                                </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td
                                        v-for="col in expandedColumns"
                                        :key="col.key"
                                        class="bg-inherit font-sans text-center"
                                    >
                                        {{ item[col.key] }}
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-sheet>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </div>
</template>

<script setup>
import { defineEmits, defineProps, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    voucher_headers: {
        type: Array,
        default: () => []
    },
    vouchers: {
        type: Array,
        default: () => []
    },
    expensesType: {
        type: String,
        default: 'Survey'
    },
    importUrl: {
        type: String,
        required: true
    },
    expandedColumns: {
        type: Array,
        default: () => []
    },
});

const search = ref('')
const itemsPerPage = ref(12)
const isLoading = ref(false)
const file = ref(null)

const emit = defineEmits(['refresh-data', 'open-dialog', 'edit-data'])

const editData = (item) => {
    emit('edit-data', item.item)
}

const addBtn = () => {
    emit('open-dialog')
}

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
        const response = await axios.post(props.importUrl, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        alert(response.data.message || 'File imported successfully!')
        emit('refresh-data')
        file.value = null
    }
    catch(error){
        console.error('Error uploading file:', error)
        alert('Error uploading file. Please try again.')
    }
    finally{
        isLoading.value = false
    }
}
</script>
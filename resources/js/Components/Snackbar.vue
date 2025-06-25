<template>
    <v-container>
        <v-snackbar
            :color="snackbarColor"
            :timeout="timeout"
            location="top right"
            v-model="snackbar"
            >
            {{ snackbarMessage }}
        </v-snackbar>
    </v-container>
</template>
    
<script setup>
import { ref, defineExpose } from 'vue';

const snackbar = ref(false);
const snackbarColor = ref('')
const snackbarMessage = ref('');
const timeout = ref(3500)

const alertSuccess = () => {
    snackbarColor.value = 'green-darken-2'
    snackbarMessage.value = 'New data has been added successfully. 🤩'
    snackbar.value = true
}

const alertError = () => {
    snackbarColor.value = 'red-darken-2'
    snackbarMessage.value = 'There was an error inserting your data. 😞'
    snackbar.value = true
}

const alertUpdate = () => {
    snackbarColor.value = 'green-darken-2'
    snackbarMessage.value = 'Your data has been updated successfully. 😊'
    snackbar.value = true
}

const alertCustom = (message) => {
    snackbarColor.value = 'cyan-accent-1'
    snackbarMessage.value = message
    snackbar.value = true
}

const alertImport = () => {
    snackbarColor.value = 'green-darken-2'
    snackbarMessage.value = 'File imported successfully!'
    snackbar.value = true
}

defineExpose({ 
    alertCustom,
    alertError,
    alertSuccess,
    alertUpdate,
    alertImport
}) 
</script>
<template>
	<v-dialog v-model="internalDialog" max-width="900px" persistent no-click-animation>
		<v-card>
			<v-toolbar style="background: linear-gradient(135deg, #0047AB, #50C878); 
                    color: white;">
                    <v-toolbar-title class="flex-grow-1">PDF Preview</v-toolbar-title>
					<v-toolbar-items>
					<v-btn icon @click="closeDialog">
						<v-icon color="red">mdi-close</v-icon>
					</v-btn>
					</v-toolbar-items>
                </v-toolbar>
			<v-card-text class="pa-0">
				<iframe v-if="pdfUrl" :src="pdfUrl" width="100%" height="600px" style="border: none;"></iframe>
				<div v-else class="text-center pa-4">No PDF to display.</div>
			</v-card-text>
		</v-card>
	</v-dialog>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';

const props = defineProps({
	dialog: {
		type: Boolean,
		required: true,
	},
	pdfUrl: {
		type: String,
		required: false,
		default: null,
	},
})

const emit = defineEmits(['update:dialog'])

const internalDialog = ref(props.dialog)

watch(() => props.dialog, (newVal) => {
	internalDialog.value = newVal
})

watch(internalDialog, (newVal) => {
	if(!newVal){
		emit('update:dialog', false)
	}
})

const closeDialog = () => {
	emit('update:dialog', false)
}
</script>
<template>
    <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2 sm:px-4 md:px-8 lg:px-16 py-4 md:py-16 justify-center">
        <button
            v-for="(survey, idx) in surveys"
            :key="survey"
            @click="openDialog(survey)"
            class="w-full h-32 sm:h-40 md:h-48 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 text-white font-semibold text-lg shadow-lg transform transition hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-blue-300"
        >
            <span>{{ survey }}</span>
        </button>
        </div>

        <!-- Dialog Modal -->
        <div v-if="dialogOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6 relative">
            <button @click="closeDialog" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-2xl">&times;</button>
            <h2 class="text-xl font-bold mb-4">{{ selectedSurvey }} Quotation</h2>
            <form @submit.prevent="submitForm">
            <!-- FROM Section (pre-filled) -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">From:</label>
                <div class="bg-gray-100 p-2 rounded text-sm">
                <div><strong>Peter June Alaan</strong></div>
                <div>Geodetic Engineer- GGSS Proprietor</div>
                <div>BS L09, Elliston Place, Pasong Camachile II, General Trias, Cavite, 4107</div>
                <div>geopetesurv2@gmail.com</div>
                <div>Mobile Nos.: 0919-0777886 (Smart) / 0915-5985620 (Globe)</div>
                </div>
            </div>
            <!-- TO Section -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Attention To:</label>
                <input v-model="form.to" type="text" class="w-full border rounded p-2" placeholder="Recipient (e.g. E.F.B. GEOSTRUKT INC.)" required />
            </div>
            <!-- Project Name -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Project Name:</label>
                <input v-model="form.projectName" type="text" class="w-full border rounded p-2" required />
            </div>
            <!-- Project Location -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Project Location:</label>
                <input v-model="form.projectLocation" type="text" class="w-full border rounded p-2" required />
            </div>
            <!-- Project Area -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Project Area (sqm):</label>
                <input v-model="form.projectArea" type="text" class="w-full border rounded p-2" required />
            </div>
            <!-- Date -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Date:</label>
                <input v-model="form.date" type="date" class="w-full border rounded p-2" required />
            </div>
            <!-- Additional Details -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Additional Details:</label>
                <textarea v-model="form.details" class="w-full border rounded p-2" rows="3" placeholder="Enter any additional details..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="closeDialog" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 text-white font-semibold hover:opacity-90">Save & Download PDF</button>
            </div>
            </form>
        </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const surveys = [
  'Aerial Topographic Survey',
  'As Built Survey',
  'Column Layout Survey',
  'Consolidation Survey',
  'Hydrographic Survey',
  'Original Survey',
  'Re Survey',
  'Relocation Survey',
  'Subdivision Survey',
  'Topographic Survey',
];

const dialogOpen = ref(false);
const selectedSurvey = ref('');
const form = ref({
  to: '',
  projectName: '',
  projectLocation: '',
  projectArea: '',
  date: '',
  details: '',
});

function openDialog(survey) {
  selectedSurvey.value = survey;
  dialogOpen.value = true;
  form.value = {
    to: '',
    projectName: '',
    projectLocation: '',
    projectArea: '',
    date: '',
    details: '',
  };
}
function closeDialog() {
  dialogOpen.value = false;
}
function submitForm() {
  // Placeholder for PDF generation logic
  alert('Form submitted! (PDF generation to be implemented)');
  closeDialog();
}
</script>

<style scoped>
/**** Responsive tweaks for buttons ****/
button {
  min-width: 0;
  word-break: break-word;
}
</style>
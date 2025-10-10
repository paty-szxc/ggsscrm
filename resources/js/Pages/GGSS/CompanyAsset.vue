<template>
    <v-container fluid>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
            <h1 class="p-1 sm:p-3 font-sans sm:indent-8 text-xl sm:text-2xl">Company Assets</h1>
        </div>
                                    <!-- NOTE - start of house & lot table -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 bg-white rounded-lg shadow p-3">
            <div class="bg-white rounded-lg shadow p-3">
                <h2 class="text-base font-semibold mb-2">House & Lot</h2>
                <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                    <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                        <v-text-field
                            clearable
                            hide-details
                            label="Search"
                            density="compact"
                            prepend-inner-icon="mdi-magnify"
                            v-model="searchHaL">
                        </v-text-field>
                    </div>
                    <div class="flex-shrink-0">
                        <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    @click="openHaLDialog('add')"
                                    color="info"
                                    icon
                                    size="small"
                                    v-bind="props">
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Add</span>
                        </v-tooltip>
                        <v-tooltip location="bottom" v-else>
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    color="grey"
                                    icon
                                    size="small"
                                    v-bind="props"
                                    style="cursor: not-allowed;"> <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Restricted: Only Admins can add.</span>
                        </v-tooltip>
                    </div>
                </div>
                <div class="overflow-x-auto mt-1 max-h-[250px] overflow-y-auto">
                    <v-data-table
                        fixed-header
                        :headers="houseHeaders"
                        :items="houses"
                        :search="searchHaL"
                        class="font-sans min-w-[400px]">
                        <template v-slot:item.pdf_attachment="{ item }">
                            <div v-if="item.pdf_url" class="pdf-attachment-wrapper">
                                <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-btn
                                        v-bind="props"
                                        color="primary"
                                        variant="text"
                                        size="large"
                                        class="!normal-case"
                                        @click="openPdfViewer(item.pdf_url)">
                                    <v-icon start color="red">mdi-file-pdf-box</v-icon>
                                    <!-- <span class="ml-2">{{ getFileName(item.pdf_url) }}</span> -->
                                    </v-btn>
                                </template>
                                <span>{{ getFileName(item.pdf_url) }}</span>
                                </v-tooltip>
                            </div>
                            <span v-else class="text-grey">No attachment</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        @click="openHaLDialog('edit', item)"
                                        v-bind="props">
                                        mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Edit</span>
                            </v-tooltip>
                            <v-tooltip location="bottom" v-else>
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="grey"
                                        v-bind="props"
                                        style="cursor: not-allowed;"> mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Restricted: Only Admins can edit.</span>
                            </v-tooltip>
                        </template>
                    </v-data-table>
                </div>
            </div>
                                    <!-- NOTE - start house & lot dialog-->
            <v-dialog class="transition-discrete md:transition-normal" persistent v-model="halDialog" width="400px" no-click-animation>
                <v-card>
                    <v-card-title 
                        style="background: linear-gradient(135deg, #0047AB, #50C878);
                        color: white;">
                        {{ halDialogTitle }}
                    </v-card-title>
                    <v-card-text>
                        <v-autocomplete
                            v-model="propertyType"
                            :items="propertyTypeOptions"
                            item-title="text"
                            item-value="value"
                            label="Select Property Type"
                            variant="outlined"
                            density="compact"
                            hide-details
                            :disabled="halDialogMode === 'edit'"
                            class="mb-3 mt-1"
                            required>
                        </v-autocomplete>
                        <v-text-field
                            hide-details
                            label="Address"
                            v-model="house_address">
                        </v-text-field>
                        <v-text-field
                            class="mt-3"
                            hide-details
                            label="Cost"
                            v-model="house_cost">
                        </v-text-field>
                        <v-file-input
                            class="mt-3"
                            density="compact"
                            label="PDF Attachment"
                            v-model="house_pdf_file"
                            accept=".pdf"
                            prepend-icon=""
                            prepend-inner-icon="mdi-file-pdf-box"
                            hide-details
                            clearable
                            variant="outlined"
                            :rules="[v => !v || v.length === 0 || v[0].size < 104857600 || 'PDF size should be less than 100 MB!']"
                        ></v-file-input>
                        <div v-if="currentPdfUrl && halDialogMode === 'edit'" class="mt-2 text-sm text-gray-600">
                            Current PDF:
                            <v-btn
                                color="primary"
                                variant="text"
                                size="small"
                                class="!normal-case"
                                @click="openPdfViewer(currentPdfUrl)">
                                <v-icon start>mdi-file-pdf-box</v-icon> View {{ getFileName(currentPdfUrl) }}
                            </v-btn>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn 
                            @click="closeHaLDialog"
                            color="red darken-1"
                            text>
                            Cancel
                        </v-btn>
                        <!-- NOTE - submit & update button for house & lot -->
                        <v-btn
                            @click="submitHaL"
                            color="green darken-1"
                            :loading="isSubmitting"
                            :disabled="isSubmitting || !house_address"
                            text>
                            {{ halDialogMode === 'add' ? 'Submit' : 'Update' }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

                                    <!-- NOTE - start of company vehicle table -->
            <div class="bg-white rounded-lg shadow p-3">
                <h2 class="text-base font-semibold mb-2">Company Vehicle</h2>
                <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                    <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="searchVC">
                    </v-text-field>
                </div>
                <div class="flex-shrink-0">
                    <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                @click="openVehicleDialog('add')"
                                color="info"
                                icon
                                size="small"
                                v-bind="props">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Add</span>
                    </v-tooltip>
                    <v-tooltip location="bottom" v-else>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                color="grey"
                                icon
                                size="small"
                                v-bind="props"
                                style="cursor: not-allowed;"> <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Restricted: Only Admins can add.</span>
                    </v-tooltip>
                </div>
            </div>
                <div class="overflow-x-auto mt-1 max-h-[250px] overflow-y-auto">
                    <v-data-table
                        fixed-footer
                        fixed-header
                        :headers="vehicleHeaders"
                        :items="vehicles"
                        :search="searchVC"
                        class="font-sans min-w-[400px]">
                        <template v-slot:item.vPdf_attachment="{ item }">
                            <div v-if="item.vehicle_pdf_url" class="pdf-attachment-wrapper">
                                <v-tooltip location="bottom">
                                    <template v-slot:activator="{ props }">
                                        <v-btn
                                        v-bind="props"
                                        color="primary"
                                        variant="text"
                                        size="large"
                                        class="!normal-case"
                                        @click="openPdfViewer(item.vehicle_pdf_url)">
                                        <v-icon start color="red">mdi-file-pdf-box</v-icon>
                                        <!-- <span class="ml-2">{{ getFileNameVehicle(item.vehicle_pdf_url) }}</span> -->
                                        </v-btn>
                                    </template>
                                    <span>{{ getFileNameVehicle(item.vehicle_pdf_url) }}</span>
                                </v-tooltip>
                            </div>
                            <span v-else class="text-grey">No attachment</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        @click="openVehicleDialog('edit', item)"
                                        v-bind="props">
                                        mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Edit</span>
                            </v-tooltip>
                            <v-tooltip location="bottom" v-else>
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="grey"
                                        v-bind="props"
                                        style="cursor: not-allowed;"> mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Restricted: Only Admins can edit.</span>
                            </v-tooltip>
                            <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        @click="viewMaintenace(item.id)"
                                        v-bind="props">
                                        mdi-eye
                                    </v-icon>
                                </template>
                                <span>View Maintenance Table</span>
                            </v-tooltip>
                        </template>
                    </v-data-table>
                </div>
            </div>
                                    <!-- NOTE - start of vehicle dialog -->
            <v-dialog class="transition-discrete md:transition-normal" persistent v-model="vehicleDialog" width="400px" no-click-animation>
                <v-card>
                    <v-card-title 
                        style="background: linear-gradient(135deg, #0047AB, #50C878); 
                        color: white;">{{ vehicleDialogTitle }}
                    </v-card-title>
                    <v-card-text>
                        <v-autocomplete
                            v-model="vehicleType"
                            :items="vehicleTypeOptions"
                            item-title="text"
                            item-value="value"
                            label="Select Vehicle Type"
                            variant="outlined"
                            density="compact"
                            hide-details
                            class="mb-3 mt-1"
                            required>
                        </v-autocomplete>
                        <v-text-field
                            hide-details
                            label="Vehicle"
                            v-model="vehicle">
                        </v-text-field>
                        <v-text-field
                            class="mt-3"
                            hide-details
                            label="Cost"
                            v-model="vehicle_cost">
                        </v-text-field>
                        <v-file-input
                            class="mt-3"
                            density="compact"
                            label="PDF Attachment"
                            v-model="vehicle_pdf_file"
                            accept=".pdf"
                            prepend-icon=""
                            prepend-inner-icon="mdi-file-pdf-box"
                            hide-details
                            clearable
                            variant="outlined"
                            :rules="[v => !v || v.length === 0 || v[0].size < 104857600 || 'PDF size should be less than 100 MB!']"
                        ></v-file-input>
                        <div v-if="currentPdfUrl && vehicleDialogMode === 'edit'" class="mt-2 text-sm text-gray-600">
                            Current PDF:
                            <v-btn
                                color="primary"
                                variant="text"
                                size="small"
                                class="!normal-case"
                                @click="openPdfViewerVehicle(currentPdfUrl)">
                                <v-icon start>mdi-file-pdf-box</v-icon> View {{ getFileNameVehicle(currentPdfUrl) }}
                            </v-btn>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn 
                            @click="closeVehicleDialog"
                            color="red darken-1"
                            text>
                            Cancel
                        </v-btn>
                        <v-btn
                            @click="submitVehicle"
                            color="green darken-1"
                            :loading="isSubmitting"
                            :disabled="isSubmitting || !vehicle"
                            text>
                            {{ vehicleDialogMode === 'add' ? 'Submit' : 'Update' }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
                                    <!-- NOTE start of personal house & lot table -->
            <div class="bg-white rounded-lg shadow p-3">
                <h2 class="text-base font-semibold mb-2">Personal House & Lot</h2>
                <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                    <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                        <v-text-field
                            clearable
                            hide-details
                            label="Search"
                            density="compact"
                            prepend-inner-icon="mdi-magnify"
                            v-model="searchPHaL">
                        </v-text-field>
                    </div>
                    <!-- <div class="flex-shrink-0">
                        <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    @click="openHaLDialog('add')"
                                    color="info"
                                    icon
                                    size="small"
                                    v-bind="props">
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Add</span>
                        </v-tooltip>
                        <v-tooltip location="bottom" v-else>
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    color="grey"
                                    icon
                                    size="small"
                                    v-bind="props"
                                    style="cursor: not-allowed;"> <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Restricted: Only Admins can add.</span>
                        </v-tooltip>
                    </div> -->
                </div>
                <div class="overflow-x-auto mt-1 max-h-[250px] overflow-y-auto">
                    <v-data-table
                        fixed-header
                        :headers="houseHeaders"
                        :items="pHouses"
                        :search="searchPHaL"
                        class="font-sans min-w-[400px]">
                        <template v-slot:item.pdf_attachment="{ item }">
                            <div v-if="item.pdf_url" class="pdf-attachment-wrapper">
                                <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-btn
                                        v-bind="props"
                                        color="primary"
                                        variant="text"
                                        size="large"
                                        class="!normal-case"
                                        @click="openPdfViewer(item.pdf_url)">
                                    <v-icon start color="red">mdi-file-pdf-box</v-icon>
                                    <!-- <span class="ml-2">{{ getFileName(item.pdf_url) }}</span> -->
                                    </v-btn>
                                </template>
                                <span>{{ getFileName(item.pdf_url) }}</span>
                                </v-tooltip>
                            </div>
                            <span v-else class="text-grey">No attachment</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        @click="openHaLDialog('edit', item)"
                                        v-bind="props">
                                        mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Edit</span>
                            </v-tooltip>
                            <v-tooltip location="bottom" v-else>
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="grey"
                                        v-bind="props"
                                        style="cursor: not-allowed;"> mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Restricted: Only Admins can edit.</span>
                            </v-tooltip>
                        </template>
                    </v-data-table>
                </div>
            </div>

                                    <!--NOTE start of personal vehicle table -->
            <div class="bg-white rounded-lg shadow p-3">
                <h2 class="text-base font-semibold mb-2">Personal Vehicle</h2>
                <div class="flex items-center w-full sm:w-auto gap-2 sm:gap-4">
                    <div class="flex-grow min-w-[150px] sm:min-w-[250px]">
                    <v-text-field
                        clearable
                        hide-details
                        label="Search"
                        density="compact"
                        prepend-inner-icon="mdi-magnify"
                        v-model="searchPV">
                    </v-text-field>
                </div>
                <!-- <div class="flex-shrink-0">
                    <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                @click="openVehicleDialog('add')"
                                color="info"
                                icon
                                size="small"
                                v-bind="props">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Add</span>
                    </v-tooltip>
                    <v-tooltip location="bottom" v-else>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                color="grey"
                                icon
                                size="small"
                                v-bind="props"
                                style="cursor: not-allowed;"> <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Restricted: Only Admins can add.</span>
                    </v-tooltip>
                </div> -->
            </div>
                <div class="overflow-x-auto mt-1 max-h-[250px] overflow-y-auto">
                    <v-data-table
                        fixed-header
                        :headers="vehicleHeaders"
                        :items="pVehicles"
                        :search="searchPV"
                        class="font-sans min-w-[400px]">
                        <template v-slot:item.vPdf_attachment="{ item }">
                            <div v-if="item.vehicle_pdf_url" class="pdf-attachment-wrapper">
                                <v-tooltip location="bottom">
                                    <template v-slot:activator="{ props }">
                                        <v-btn
                                        v-bind="props"
                                        color="primary"
                                        variant="text"
                                        size="large"
                                        class="!normal-case"
                                        @click="openPdfViewer(item.vehicle_pdf_url)">
                                        <v-icon start color="red">mdi-file-pdf-box</v-icon>
                                        <!-- <span class="ml-2">{{ getFileNameVehicle(item.vehicle_pdf_url) }}</span> -->
                                        </v-btn>
                                    </template>
                                    <span>{{ getFileNameVehicle(item.vehicle_pdf_url) }}</span>
                                </v-tooltip>
                            </div>
                            <span v-else class="text-grey">No attachment</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        @click="openVehicleDialog('edit', item)"
                                        v-bind="props">
                                        mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Edit</span>
                            </v-tooltip>
                            <v-tooltip location="bottom" v-else>
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="grey"
                                        v-bind="props"
                                        style="cursor: not-allowed;"> mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Restricted: Only Admins can edit.</span>
                            </v-tooltip>
                            <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        @click="viewMaintenace(item.id)"
                                        v-bind="props">
                                        mdi-eye
                                    </v-icon>
                                </template>
                                <span>View Maintenance Table</span>
                            </v-tooltip>
                        </template>
                    </v-data-table>
                </div>
            </div>
        </div>

        <PdfViewerDialog 
            :dialog="pdfViewerDialog" 
            :pdf-url="currentPdfUrl" 
            @update:dialog="pdfViewerDialog = $event">
        </PdfViewerDialog>

                                        <!-- NOTE - maintenance table dialog -->
        <v-dialog class="transition-discrete md:transition-normal" persistent v-model="maintenanceDialog" width="850">
            <v-card elevation="10" class="d-flex flex-column" style="min-height: 500px;">
                <v-toolbar style="background: linear-gradient(135deg, #0047AB, #50C878); color: white;">
                    <v-toolbar-title>Maintenance Table - {{ currentVehicleName }}</v-toolbar-title>
                    <v-toolbar-items>
                        <v-btn icon @click="maintenanceDialog = false">
                            <v-icon color="red">mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                
                <div class="overflow-x-auto">
                    <div class="d-flex justify-end ma-4">
                        <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    @click="openMaintenanceDialog('add', currentVehicleId)"
                                    color="info"
                                    v-bind="props" >
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Add</span>
                        </v-tooltip>
                        <v-tooltip location="bottom" v-else>
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    color="grey"
                                    icon
                                    size="small"
                                    v-bind="props"
                                    style="cursor: not-allowed;">
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Restricted: Only Admins can add.</span>
                        </v-tooltip>
                    </div>
                    <v-data-table
                        density="compact"
                        fixed-header
                        fixed-footer
                        items-per-page="5"
                        :headers="headers"
                        :items="maintenance"
                        class="font-sans min-w-[400px]">
                        <template v-slot:item.pdf_att="{ item }">
                            <div v-if="item.maintenance_url" class="pdf-attachment-wrapper">
                                <v-tooltip location="bottom">
                                    <template v-slot:activator="{ props }">
                                        <v-btn
                                        v-bind="props"
                                        color="primary"
                                        variant="text"
                                        size="large"
                                        class="!normal-case"
                                        @click="openPdfViewer(item.maintenance_url)">
                                        <v-icon start color="red">mdi-file-pdf-box</v-icon>
                                        <!-- <span class="ml-2">{{ getFileNameVehicle(item.maintenance_url) }}</span> -->
                                        </v-btn>
                                    </template>
                                    <span>{{ getFileNameVehicle(item.maintenance_url) }}</span>
                                </v-tooltip>
                            </div>
                            <span v-else class="text-grey">No attachment</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-tooltip location="bottom" v-if="userCanEditOrAdd">
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        @click="openMaintenanceDialog('edit', null, item)"
                                        v-bind="props"> mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Edit</span>
                            </v-tooltip>
                            <v-tooltip location="bottom" v-else>
                                <template v-slot:activator="{ props }">
                                    <v-icon
                                        size="small"
                                        class="me-2"
                                        color="info"
                                        v-bind="props"
                                        disabled> mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Restricted: Only Admins can edit.</span>
                            </v-tooltip>
                        </template>
                    </v-data-table>
                </div>
            </v-card>
        </v-dialog>
                                            <!-- NOTE - maintenance add dialog -->
        <v-dialog class="transition-discrete md:transition-normal" persistent v-model="addDataDialog" width="500">
            <v-card>
                <v-card-title 
                    style="background: linear-gradient(135deg, #0047AB, #50C878); 
                    color: white;">{{ maintenaceDialogTitle }}
                </v-card-title>
                <v-card-text>
                    <v-form ref="maintenanceForm">
                        <!-- vehicle selection (only show when adding new record) -->
                        <v-select
                            v-if="maintenaceDialogMode === 'add'"
                            v-model="selectedVehicle"
                            :items="vehicles"
                            item-title="vehicle_name"
                            item-value="id"
                            label="Select Vehicle"
                            :rules="[v => !!v || 'Vehicle is required']"
                            class="mb-4"
                            density="compact"
                            required
                            hide-details
                            variant="outlined"
                            :disabled="!!currentVehicleId">
                        </v-select>
                        <!-- show message when vehicle is pre-selected -->
                        <div v-if="maintenaceDialogMode === 'add' && currentVehicleId" class="mb-3 text-sm text-blue-600">
                            <!-- adding maintenance for: <strong>{{ currentVehicleName }}</strong> -->
                        </div>
                        <!-- maintenance details -->
                        <v-date-input
                            class="mt-3"
                            density="compact"
                            hide-details
                            label="Date"
                            prepend-icon=""
                            prepend-inner-icon="mdi-calendar"
                            :rules="[v => !!v || 'Date is required']"
                            required
                            style="width: 100%"
                            variant="outlined"
                            v-model="date"
                            :max="new Date().toISOString().split('T')[0]">
                        </v-date-input>
                        <v-text-field
                            class="mt-3"
                            v-model="amount"
                            label="Amount"
                            prefix="₱"
                            @input="handleCurrencyInput('amount')"
                            @blur="formatCurrencyField('amount')"
                            :rules="[v => !!v || 'Amount is required', v => !isNaN(parseFloat(v.replace(/[^0-9.]/g, ''))) || 'Please enter a valid amount']">
                        </v-text-field>
                        <v-textarea
                            density="compact"
                            hide-details
                            label="Particulars"
                            rows="2"
                            variant="outlined"
                            v-model="particulars"
                            :rules="[v => !!v || 'Particulars is required', v => v.length <= 255 || 'Particulars must be less than 255 characters']">
                        </v-textarea>
                        <v-text-field
                            class="mt-3"
                            v-model="materials"
                            label="Materials Used">
                        </v-text-field>
                        <v-file-input
                            class="mt-3"
                            density="compact"
                            label="Select File"
                            v-model="maintenance_file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            prepend-icon=""
                            prepend-inner-icon="mdi-file"
                            hide-details
                            clearable
                            variant="outlined"
                            :rules="[
                                v => {
                                    if (!v || v.length === 0) return true
                                    const file = v[0]
                                    if (!file) return true
                                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png']
                                    return (validTypes.includes(file.type) && file.size < 10485760) || 
                                        'File must be PDF, JPG, or PNG and less than 10 MB'
                                }
                            ]"
                        />
                        <div v-if="currentPdfUrl && maintenaceDialogMode === 'edit'" class="mt-2 text-sm text-gray-600">
                            Current PDF:
                            <v-btn
                                color="primary"
                                variant="text"
                                size="small"
                                class="!normal-case"
                                @click="openPdfViewer(currentPdfUrl)">
                                <v-icon start>mdi-file-box</v-icon> View
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn 
                        @click="closeAddDataDialog()"
                        color="red darken-1"
                        text>
                        Cancel
                    </v-btn>
                    <v-btn
                        @click="submitForm"
                        color="green darken-1"
                        :loading="isSubmitting"
                        text>
                        {{ maintenaceDialogMode === 'add' ? 'Submit' : 'Update' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <Snackbar ref="snackbar"></Snackbar>
    </v-container>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import PdfViewerDialog from '../../Components/PdfViewerDialog.vue'
import Snackbar from '../../Components/Snackbar.vue'

const isSubmitting = ref(false)
const propertyType = ref('company')
const propertyTypeOptions = ref([
    { text: 'Company Asset (House & Lot)', value: 'company' },
    { text: 'Personal Asset (House & Lot)', value: 'personal' },
])

const vehicleType = ref('company')
const vehicleTypeOptions = ref([
    { text: 'Company Vehicle', value: 'company' },
    { text: 'Personal Vehicle', value: 'personal' },
]);

const headers = ref([
    { title: 'Date', value: 'date', align: 'center'},
    { title: 'Particulars', value: 'particulars', align: 'center'},
    { title: 'Materials', value: 'materials', align: 'center'},
    { title: 'Amount', value: 'formattedAmount', align: 'center'},
    { title: 'File', value: 'pdf_att', align: 'center'},
    { title: 'Actions', value: 'actions', align: 'center'}
])

const maintenance = ref([])
const addDataDialog = ref(false)
const editingMaintencance = ref(null)
const date = ref('')
const particulars = ref('')
const materials = ref('')
const amount = ref('')
const maintenance_file = ref(null)
const maintenanceForm = ref(null)
const selectedVehicle = ref(null)
const maintenanceDialog = ref(false)
const currentVehicleId = ref(null)
const currentVehicleName = ref('')
const current_maintenance_url = ref(null)


const submitForm = async () => {
    isSubmitting.value = true
    const { valid } = await maintenanceForm.value.validate()
    if(!valid) return

    //ensure vehicle is selected for new records
    if(maintenaceDialogMode.value === 'add' && !selectedVehicle.value){
        snackbar.value.alertCustom('Vehicle is required. Please select a vehicle.')
        return
    }

    //prepare the FormData object for handling files
    const formData = new FormData()
    formData.append('company_vehicle_id', selectedVehicle.value)
    formData.append('date', formatDateForBackend(date.value))
    formData.append('particulars', particulars.value)
    formData.append('materials', materials.value)
    formData.append('amount', parseFloat(amount.value.replace(/[^0-9.]/g, '')))

    //append the file if one is selected
    if(maintenance_file.value) {
        formData.append('attachment', maintenance_file.value) // Replace 'file_field_name' with the name your backend expects
    }

    //if it's an 'edit' operation, you might need to handle the _method field for some frameworks
    if(maintenaceDialogMode.value === 'edit'){
        formData.append('_method', 'put')
    }

    try{
        const url = maintenaceDialogMode.value === 'edit' && editingMaintencance.value?.id ?
            `update_data_in_maintenance_table/${editingMaintencance.value.id}` :
            'insert_data_in_maintenance_table'

        const response = await axios({
            method: 'post', //always use 'post' when sending FormData, and handle 'put' with the _method field
            url,
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data', //this is crucial for file uploads
            }
        })

        snackbar.value.alertCustom(maintenaceDialogMode.value === 'edit' ?
            'Record updated successfully!' :
            'Record created successfully!')

        //refresh maintenance data for the current vehicle
        fetchMaintenanceTableData(currentVehicleId.value)
        addDataDialog.value = false
        resetForm()
    }
    catch (error) {
        console.error('Error:', error)
        const errorMsg = error.response?.data?.message ||
            error.response?.data?.errors ||
            'An error occurred'
        snackbar.value.alertCustom(errorMsg)
    }
    finally {
        isSubmitting.value = false
    }
}

const viewMaintenace = (vehicleId) =>{
    maintenanceDialog.value = true
    //store the vehicle ID for when adding new maintenance records
    selectedVehicle.value = vehicleId
    currentVehicleId.value = vehicleId
    
    //find the vehicle name for display
    const vehicle = vehicles.value.find(v => v.id === vehicleId)
    currentVehicleName.value = vehicle ? vehicle.vehicle_name : 'Unknown Vehicle'
    
    //fetch maintenance data for this specific vehicle
    fetchMaintenanceTableData(vehicleId)
}

const maintenaceDialogTitle = computed(() => {
    return maintenaceDialogMode.value === 'add' ? 'Add' : 'Edit'
})

const maintenaceDialogMode = ref('add')

const closeAddDataDialog = () => {
    addDataDialog.value = false
    resetForm()
    //reset form validation
    if(maintenanceForm.value){
        maintenanceForm.value.resetValidation()
    }
}

const openMaintenanceDialog = (mode, vehicleId = null, item = null) => { //NOTE - add & edit maintenance dialog
    maintenaceDialogMode.value = mode //set the mode here
    addDataDialog.value = true

    if(mode === 'add'){
        selectedVehicle.value = vehicleId //set the vehicle ID when adding
        resetForm()
        // If we're in the context of a specific vehicle, ensure it's selected
        if(currentVehicleId.value && !vehicleId){
            selectedVehicle.value = currentVehicleId.value
        }
    } 
    else if(mode === 'edit' && item){
        editingMaintencance.value = item
        selectedVehicle.value = item.company_vehicle_id //use the existing vehicle ID
        date.value = item.date
        particulars.value = item.particulars
        materials.value = item.materials || ''
        amount.value = item.amount ? item.amount.toString() : ''
        current_maintenance_url.value = item.maintenance_url //set the current PDF URL for display
        
        //ensure we're still in the context of the current vehicle
        if(currentVehicleId.value && currentVehicleId.value !== item.company_vehicle_id){
            currentVehicleId.value = item.company_vehicle_id
            const vehicle = vehicles.value.find(v => v.id === item.company_vehicle_id)
            currentVehicleName.value = vehicle ? vehicle.vehicle_name : 'Unknown Vehicle'
        }
    }
}

const resetForm = () => {
    date.value = ''
    particulars.value = ''
    materials.value = ''
    amount.value = ''
    maintenance_file.value = null
    current_maintenance_url.value = null
    editingMaintencance.value = null
    maintenaceDialogMode.value = 'add'
}

//helper function to format date
const formatDateForBackend = (dateValue) => {
    if(!dateValue) return null

    //if the dateValue is already a Date object, use it directly
    const d = dateValue instanceof Date ? dateValue : new Date(dateValue)

    //check if the date is valid
    if(isNaN(d.getTime())) return null

    //use the local date components to avoid timezone shifts
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')

    return `${year}-${month}-${day}`
}

const handleCurrencyInput = (field) => {
    //remove all non-numeric characters except decimal point
    let value = amount.value
    if (typeof value === 'string') {
        //remove commas and other non-numeric characters except decimal
        value = value.replace(/[^\d.]/g, '')
        
        //ensure only one decimal point
        const parts = value.split('.')
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('')
        }
        
        amount.value = value
    }
}

const formatCurrencyField = (field) => {
    let value = amount.value
    if (value && value !== '') {
        //parse the numeric value
        const num = parseFloat(value)
        if (!isNaN(num)) {
            //format with commas for display
            amount.value = num.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            })
        }
    }
}//SECTION - end of maintenance table & dialog functions

//SECTION - user role computed properties
const userIsAdmin = computed(() => {
    const userId = usePage().props.auth?.user?.role
    //assuming user IDs 1, 2, and 12 are admins. Adjust as per your actual user roles.
    return ['Admin'].includes(userId)
})

const userCanEditOrAdd = computed(() => {
    //only admins can add or edit
    return userIsAdmin.value
})
//END SECTION - user role computed properties

//!SECTION - pdf viewer variable
const pdfViewerDialog = ref(false);
const currentPdfUrl = ref(null);

const openPdfViewer = (url) => {
    currentPdfUrl.value = url;
    pdfViewerDialog.value = true;
};

//SECTION - start of house & lot variables
const houseHeaders = ref([
    { title: 'Address', value: 'address', align: 'center'},
    { title: 'Cost', value: 'formattedCost', align: 'center'},
    { title: 'Attachment', value: 'pdf_attachment', align: 'center'},
    { title: 'Actions', value: 'actions', align: 'center'}
])
const houses = ref([])
const pHouses = ref([])
const house_address = ref('')
const house_cost = ref('')
const searchHaL = ref('')
const searchPHaL = ref('')
const house_pdf_file = ref(null)
const halDialog = ref(false)
const halDialogMode = ref('add')
const editingHaL = ref(null) //NOTE - end of house & lot variables


//SECTION - start of vehicle variables
const vehicleHeaders = ref([
    { title: 'Vehicle', value: 'vehicle_name', align: 'center'},
    { title: 'Cost', value: 'formattedCost', align: 'center'},
    { title: 'Attachment', value: 'vPdf_attachment', align: 'center'},
    { title: 'Actions', value: 'actions', align: 'center'}
])
const vehicles = ref([])
const pVehicles = ref([])
const vehicle = ref('')
const vehicle_cost = ref('')
const vehicle_pdf_file = ref(null)
const searchVC = ref('')
const searchPV = ref('')
const vehicleDialog = ref(false)
const vehicleDialogMode = ref('add')
const editingVehicle = ref(null) //NOTE - end of vehicle variables

const snackbar = ref(null)

//!SECTION - functions
const getFileName = (url) => {
    return url.split('/').pop() || 'document.pdf'
}
const getFileNameVehicle = (url) => {
    return url.split('/').pop() || 'document.pdf'
}

//!SECTION - start of vehicle functions
const submitVehicle = async () => { //NOTE - start of submit & update function for company vehicle
    isSubmitting.value = true
    const formData = new FormData()
    formData.append('vehicle_type', vehicleType.value)
    formData.append('vehicle_name', vehicle.value)
    formData.append('cost', vehicle_cost.value !== null ? String(vehicle_cost.value) : '')

    //ANCHOR - for debugging ensure you're properly handling the file input
    const fileToAppend = vehicle_pdf_file.value instanceof File 
        ? vehicle_pdf_file.value 
        : (vehicle_pdf_file.value?.[0] instanceof File ? vehicle_pdf_file.value[0] : null)

        if(fileToAppend){
            formData.append('pdf_file', fileToAppend)
            console.log('Appending PDF file:', fileToAppend.name)
        }
        else{
            console.log('No new PDF file selected for upload or file object is invalid.')
        }

        
        if(vehicleDialogMode.value === 'edit'){
            //crucial for Laravel to interpret this as a PUT request
            formData.append('_method', 'PUT') 
        }

        //log FormData contents (for debugging)
        for(let [key, value] of formData.entries()){
            console.log(`${key}: ${value}`)
        }

        try{
            let res
            if(vehicleDialogMode.value === 'add'){
                res = await axios.post('insert_company_vehicle', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                })
                fetchCompanyVehicle()
                fetchPersonalVehicle()
            } 
            else{
                res = await axios.post(`update_company_vehicle/${editingVehicle.value.id}`, formData, { 
                headers: { 
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PUT' //alternative to _method
                },
            })
            fetchCompanyVehicle()
            fetchPersonalVehicle()
        }

        if(res.data.success){
            if(vehicleDialog === 'add'){
                snackbar.value.alertSuccess(res.data.message)
            }
            else{
                snackbar.value.alertUpdate()
            }
            closeVehicleDialog()
            await fetchCompanyVehicle()
        }
        else{
            if(res.data.errors){
                const errorMessages = Object.values(res.data.errors).flat().join('\n')
                snackbar.value.alertWarning(`Validation failed:\n${errorMessages}`)
            }
            else{
                snackbar.value.alertError(res.data.message)
            }
        }
    } 
    catch(err){
        if(err.response && err.response.data && err.response.data.errors){
            const errorMessages = Object.values(err.response.data.errors).flat().join('\n')
            snackbar.value.alertWarning(`Validation failed. Please check your inputs:\n${errorMessages}`)
        }
        else{
            snackbar.value.alertWarning('An error occurred. Please try again later.')
            console.error('Error submitting Vehicle data:', err)
        }
    }
    finally {
        isSubmitting.value = false
    }
} //NOTE - end of submit & update function for vehicle 

const closeVehicleDialog = () => { //NOTE - close vehicle dialog
    vehicleDialog.value = false
    vehicle.value = ''
    vehicle_cost.value = ''
    vehicle_pdf_file.value = null
    currentPdfUrl.value = null
    editingVehicle.value = null
    vehicleDialogMode.value = 'add' //reset mode to 'add' for next time
    vehicleType.value = 'company'
}


const openVehicleDialog = (mode, item = null) => { //NOTE - add & edit vehicle dialog
    vehicleDialogMode.value = mode //set the mode here!
    vehicleDialog.value = true
    vehicle_pdf_file.value = null //clear previous file selection

    if(mode === 'edit' && item){
        console.log(item)
        editingVehicle.value = item
        vehicle.value = item.vehicle_name
        vehicle_cost.value = item.originalCost
        currentPdfUrl.value = item.vehicle_pdf_url //set the current PDF URL for display
        vehicleType.value = item.vehicle_type
    } 
    else{
        editingVehicle.value = null
        vehicle.value = ''
        vehicle_cost.value = ''
        currentPdfUrl.value = null //clear current PDF URL for add mode
    }
} 

const vehicleDialogTitle = computed(() => {
    return vehicleDialogMode.value === 'add' ? 'Add Vehicle' : 'Edit Vehicle'
})
//NOTE - end of vehicle functions

//NOTE -  start of house & lot  functions
const submitHaL = async () => { //NOTE - start of submit & update button for house & lot
    isSubmitting.value = true
    const formData = new FormData()
    formData.append('property_type', propertyType.value) 
    formData.append('address', house_address.value)
    formData.append('cost', house_cost.value !== null ? String(house_cost.value) : '')
    
    //ANCHOR - for debugging ensure you're properly handling the file input
    const fileToAppend = house_pdf_file.value instanceof File 
        ? house_pdf_file.value 
        : (house_pdf_file.value?.[0] instanceof File ? house_pdf_file.value[0] : null)

        if(fileToAppend){
            formData.append('pdf_file', fileToAppend)
            console.log('Appending PDF file:', fileToAppend.name)
        }
        else{
            console.log('No new PDF file selected for upload or file object is invalid.')
        }

        
        if(halDialogMode.value === 'edit'){
            // Crucial for Laravel to interpret this as a PUT request
            formData.append('_method', 'PUT') 
        }

        // Log FormData contents (for debugging)
        for(let [key, value] of formData.entries()){
            console.log(`${key}: ${value}`)
        }

    try{
        let res
            if(halDialogMode.value === 'add'){
                res = await axios.post('insert_house_and_lot', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                })
                fetchHouseAndLot()
                fetchPersonalHouseAndLot()
            } 
            else{
                // Pass the ID in the URL for update operations
                // res = await axios.post(`update_house_and_lot/${editingHaL.value.id}`, formData, { 
                //     headers: { 'Content-Type': 'multipart/form-data' },
                // })
                res = await axios.post(`update_house_and_lot/${editingHaL.value.id}`, formData, { 
                headers: { 
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PUT' // Alternative to _method
                },
            })
            fetchHouseAndLot()
            fetchPersonalHouseAndLot()
        }

        if(res.data.success){
            if(halDialogMode === 'add'){
                snackbar.value.alertSuccess(res.data.message)
            }
            else{
                snackbar.value.alertUpdate()
            }
            closeHaLDialog()
            await fetchHouseAndLot()
        }
        else{
            if(res.data.errors){
                const errorMessages = Object.values(res.data.errors).flat().join('\n')
                snackbar.value.alertWarning(`Validation failed:\n${errorMessages}`)
            }
            else{
                snackbar.value.alertError(res.data.message)
            }
        }
    } 
    catch(err){
        if(err.response && err.response.data && err.response.data.errors){
            const errorMessages = Object.values(err.response.data.errors).flat().join('\n')
            snackbar.value.alertWarning(`Validation failed. Please check your inputs:\n${errorMessages}`)
        }
        else{
            snackbar.value.alertWarning('An error occurred. Please try again later.')
            console.error('Error submitting House & Lot data:', err)
        }
    }
    finally {
        isSubmitting.value = false
    }
} //NOTE - end of submit & update button for house & lot dialog

const halDialogTitle = computed(() => {
    return halDialogMode.value === 'add' ? 'Add House & Lot' : 'Edit House & Lot'
})

const closeHaLDialog = () => {
    halDialog.value = false
    house_address.value = ''
    house_cost.value = ''
    house_pdf_file.value = null
    currentPdfUrl.value = null
    editingHaL.value = null
    halDialogMode.value = 'add' //reset mode to 'add' for next time
    propertyType.value = 'company'
}

const openHaLDialog = (mode, item = null) => { //NOTE - house & lot dialog
    halDialogMode.value = mode //set the mode here!
    halDialog.value = true
    house_pdf_file.value = null //clear previous file selection

    if(mode === 'edit' && item){
        console.log(item)
        editingHaL.value = item
        house_address.value = item.address
        house_cost.value = item.originalCost
        currentPdfUrl.value = item.pdf_url //set the current PDF URL for display
        propertyType.value = item.property_type
    }
    else{
        editingHaL.value = null
        house_address.value = ''
        house_cost.value = ''
        currentPdfUrl.value = null //clear current PDF URL for add mode
    }
} //!SECTION - end of house & lot functions

const formatCurrency = (value) => {
    //convert to number first
    const num = parseFloat(value)
    
    //return empty string for 0, null, undefined, or NaN
    if (!num || isNaN(num)) return ''
    
    //format valid numbers
    return `₱ ${num.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`
}

const fetchMaintenanceTableData = async (vehicleId = null) => {
    try{
        const url = vehicleId 
            ? `get_maintenance_table_data?vehicle_id=${vehicleId}`
            : 'get_maintenance_table_data'
        const res = await axios.get(url)
        maintenance.value = res.data.map(item => ({
            ...item,
            formattedAmount: formatCurrency(item.amount),
            originalAmount: item.amount //keep original value if needed
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

const fetchHouseAndLot = async () => {
    try{
        const res = await axios.get('get_house_and_lot')
        houses.value = res.data.map(item => ({
            ...item,
            formattedCost: formatCurrency(item.cost),
            originalCost: item.cost //keep original value if needed
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

const fetchPersonalHouseAndLot = async () => {
    try{
        const res = await axios.get('get_personal_house_and_lot')
        pHouses.value = res.data.map(item => ({
            ...item,
            formattedCost: formatCurrency(item.cost),
            originalCost: item.cost //keep original value if needed
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}
const fetchCompanyVehicle = async () => {
    try{
        const res = await axios.get('get_company_vehicle')
        vehicles.value = res.data.map(item => ({
            ...item,
            formattedCost: formatCurrency(item.cost),
            originalCost: item.cost //keep original value if needed
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

const fetchPersonalVehicle = async () => {
    try{
        const res = await axios.get('get_personal_vehicle')
        pVehicles.value = res.data.map(item => ({
            ...item,
            formattedCost: formatCurrency(item.cost),
            originalCost: item.cost //keep original value if needed
        }))
    }
    catch(error){
        console.error('Error fetching data.', error)
    }
}

onMounted(() => {
    fetchCompanyVehicle()
    fetchHouseAndLot()
    fetchPersonalHouseAndLot()
    fetchPersonalVehicle()
    //don't fetch all maintenance data initially - only when viewing specific vehicle
})
</script>

<style scoped>
.pdf-attachment-wrapper{
    display: inline-flex;
    align-items: center;
}
</style>
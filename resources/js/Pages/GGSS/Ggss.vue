<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 px-2 sm:px-4 md:px-8 lg:px-16 py-4 md:py-16 justify-center">
        <a :href="userIsAdmin ? '/dashboard' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                        <img src="/public/images/dashboard.png" alt="Dashboard" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/dashboard.png" alt="Equipment" 
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
                </div>
            </template>
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span>Dashboard</span>
            </div>
        </a>
        <a href="survey_monitoring" class="block cursor-pointer flex flex-col items-center">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/task-management.png" alt="Project Monitoring" class="h-60 shadow-xl/30 -inset-2 rounded-lg">
            </div>
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span>Project Monitoring</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/sales_and_revenue' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                            <img src="/public/images/revenue.png" alt="Sales & Revenue" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/revenue.png" alt="Equipment" 
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
                </div>
            </template>
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span>Sales & Revenue</span>
            </div>
        </a>
        <!-- <a :href="userIsAdmin ? '/survey_quotation' : null" 
            :class="{'flex flex-col items-center': true, 'cursor-not-allowed opacity-20': userIsAdmin, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                text="Still Ongoing"
                location="bottom">
                <template v-slot:activator="{ props }">
                <div v-bind="props">
                    <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img 
                        src="/public/images/quotation.png" 
                        alt="Quotations" 
                        :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-20': userIsAdmin, 'opacity-20': !userIsAdmin}">
                    </div>
                    <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                    <span>Quotations</span>
                    </div>
                </div>
                </template>
            </v-tooltip>
        </a> -->
        <a :href="userIsAdmin ? '/survey_quotation' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                            <img src="/public/images/quotation.png" alt="Quotations" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/quotation.png" alt="Equipment" 
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
                </div>
            </template>
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span>Quotations</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/expenses' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                        <img src="/public/images/salary.png" alt="Expenses" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-20': !userIsAdmin}">
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/salary.png" alt="Equipment" 
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
                </div>
            </template>
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span>Expenses</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/office_equipment' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                        <img src="/public/images/office.png" alt="Equipment" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/office.png" alt="Equipment" 
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
                </div>
            </template>
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span>Office Equipments</span>
            </div>
        </a>
        <a :href="(userIsAdmin || userIsViewer) ? '/survey_equipments' : null"
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !(userIsAdmin || userIsViewer)}">
            <v-tooltip
                v-if="!(userIsAdmin || userIsViewer)"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                            <img src="/public/images/equipment.png" alt="Asset"
                                :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin || userIsViewer">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/equipment.png" alt="Asset"
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
                </div>
            </template>
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span>Survey Equipments</span>
            </div>
        </a>
        <!-- <a :href="(userIsAdmin || userIsViewer) ? '/company_assets' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!(userIsAdmin || userIsViewer)"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                            <img src="/public/images/asset.png" alt="Asset" 
                                :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                        <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                            <span>Company Assets</span>
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="(userIsAdmin || userIsViewer)">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/asset.png" alt="Asset" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                </div>
                <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                    <span>Company Assets</span>
                </div>
            </template>
        </a> -->
        <a :href="(userIsAdmin || userIsViewer || userIsViewer1) ? '/company_assets' : null"
        :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !(userIsAdmin || userIsViewer || userIsViewer1)}">
        <v-tooltip
            v-if="!(userIsAdmin || userIsViewer || userIsViewer1)"
            text="Access restricted to authorized users only."
            location="bottom">
            <template v-slot:activator="{ props }">
                <div v-bind="props">
                    <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/asset.png" alt="Asset"
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-50">
                    </div>
                </div>
            </template>
        </v-tooltip>
        <template v-if="userIsAdmin || userIsViewer || userIsViewer1">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/asset.png" alt="Asset"
                    class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
            </div>
        </template>
        <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
            <span>Company Assets</span>
        </div>
        </a>
        <a :href="(userIsAdmin || userIsViewer1) ? '/govt_external' : null"
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !(userIsAdmin || userIsViewer1)}">
            <v-tooltip
                v-if="!(userIsAdmin || userIsViewer1)"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                            <img src="/public/images/government.png" alt="Govt"
                                :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75': (userIsAdmin || userIsViewer1), 'opacity-50': !(userIsAdmin || userIsViewer1)}">
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin || userIsViewer1">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/government.png" alt="Govt"
                        class="h-60 shadow-xl/30 -inset-2 rounded-lg object-contain opacity-75">
                </div>
            </template>
            
            <div class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center">
                <span class="leading-tight">Government Related<br>(External Affairs)</span>
            </div>
        </a>
    </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

// const isHomePage = computed(() => usePage().url === '/')
// const showImages = computed(() => isHomePage.value)
const userIsAdmin = computed(() => {
    const userRole = usePage().props.auth?.user?.role
    console.log('User  data:', userRole)
    return ['Admin'].includes(userRole)
})

const userIsViewer = computed(() => {
    const authUser = usePage().props.auth?.user
    //return false if authUser is null/undefined, indicating data isn't loaded yet
    if(!authUser) return false

    const userRole = authUser.role
    const userEmpCode = authUser.emp_code
    const allowedViewerEmpCodes = ['0000-0003']
    console.log('Emp Code:', userEmpCode);
    
    return ['Encoder & Viewer'].includes(userRole) && allowedViewerEmpCodes.includes(userEmpCode)
})

const userIsViewer1 = computed(() => {
    const authUser = usePage().props.auth?.user
    //return false if authUser is null/undefined, indicating data isn't loaded yet
    if(!authUser) return false

    const userRole = authUser.role
    const userEmpCode = authUser.emp_code
    const allowedViewerEmpCodes = ['0000-0005']
    console.log('Emp Code:', userEmpCode);
    
    return ['Encoder & Viewer'].includes(userRole) && allowedViewerEmpCodes.includes(userEmpCode)
    
})

const currentUser = ref(null)
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

onMounted(() => {
    fetchCurrentUser()
})
</script>
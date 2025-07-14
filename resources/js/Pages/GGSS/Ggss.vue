<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2 sm:px-4 md:px-8 lg:px-16 py-4 md:py-16 justify-center">
        <a :href="userIsAdmin ? '/dashboard' : null" 
            :class="{'block cursor-pointer flex flex-col items-center': true, 'disabled-link': !userIsAdmin}">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/dashboard.png" alt="Dashboard" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
            </div>
                <div class="m-4 font-semibold">
                    <span>Dashboard</span>
                </div>
        </a>
        <a href="survey_monitoring" class="block cursor-pointer flex flex-col items-center">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/task-management.png" alt="Project Monitoring" class="h-60 shadow-xl/30 -inset-2 rounded-lg">
            </div>
            <div class="m-4 font-semibold">
                <span>Project Monitoring</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/sales_and_revenue' : null" 
            :class="{'block cursor-pointer flex flex-col items-center': true, 'disabled-link': !userIsAdmin}">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/revenue.png" alt="Sales & Revenue" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-obtain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
            </div>
            <div class="m-4 font-semibold">
                <span>Sales & Revenue</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/survey_quotation' : null" 
            :class="{'block cursor-pointer flex flex-col items-center': true, 'disabled-link': !userIsAdmin}">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/quotation.png" alt="Quotations" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-obtain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
            </div>
            <div class="m-4 font-semibold">
                <span>Quotations</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/expenses' : null" 
            :class="{'block cursor-pointer flex flex-col items-center': true, 'disabled-link': !userIsAdmin}">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/salary.png" alt="Expenses" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-obtain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
            </div>
            <div class="m-4 font-semibold">
                <span>Expenses</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/office_equipment' : null" 
            :class="{'block cursor-pointer flex flex-col items-center': true, 'disabled-link': !userIsAdmin}">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/equipment.png" alt="Equipment" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-obtain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                </div>
                <div class="m-4 font-semibold">
                    <span>Office Equipments</span>
                </div>
        </a>
        <a :href="userIsAdmin ? '/company_assets' : null" 
            :class="{'block cursor-pointer flex flex-col items-center': true, 'disabled-link': !userIsAdmin}">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/asset.png" alt="Asset" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
            </div>
            <div class="m-4 font-semibold">
                <span>Company Assets</span>
            </div>
        </a>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// const isHomePage = computed(() => usePage().url === '/')
// const showImages = computed(() => isHomePage.value)
const userIsAdmin = computed(() => {
    console.log('User data:', usePage().props.auth?.user)
    return usePage().props.auth?.user?.role === 'Admin'
})
</script>
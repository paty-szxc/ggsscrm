<template>
    <div>
        <header v-if="!isLoginPage" class="bg-gradient-to-b from-green-500 to-blue-500 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Back button - shows when NOT on home page or login page -->
                <nav v-if="showBackButton" class="flex">
                    <v-tooltip :text="backButtonTooltip" location="bottom">
                        <template v-slot:activator="{ props }">
                            <a :href="backButtonLink" v-bind="props">
                                <v-icon>mdi-arrow-left-bold-box-outline</v-icon>
                            </a>
                        </template>
                    </v-tooltip>
                </nav>
                <h1 class="font-medium text-xl">{{ headerTitle }}</h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <!-- <img 
                        :src="userAvatar" 
                        alt="User Avatar" 
                        class="w-8 h-8 rounded-full object-cover border-2 border-white"
                    > -->
                    <span class="hidden sm:inline">{{ userName }}</span>
                </div>

                <v-tooltip text="Logout" location="bottom">
                    <template v-slot:activator="{ props }">
                        <button @click="logout" v-bind="props" class="flex items-center space-x-1 p-2 rounded">
                            <v-icon>mdi-logout</v-icon>
                        </button>
                    </template>
                </v-tooltip>
            </div>
        </header>

        <main>
            <div v-if="showImages" class="justify-center grid lg:grid-flow-col grid-rows-1 gap-12 px-16 md:py-16">
                <a :href="userIsAdmin ? '/dashboard' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/dashboard.png" alt="Dashboard" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        <div class="m-4 text-center font-semibold">
                        <span>Dashboard</span>
                    </div>
                </a>
                <a href="survey_monitoring" class="block cursor-pointer">
                    <img src="/public/images/task-management.png" alt="Project Monitoring" class="h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <div class="m-4 text-center font-semibold">
                        <span>Project Monitoring</span>
                    </div>
                </a>
                <a :href="userIsAdmin ? '/sales_and_revenue' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/revenue.png" alt="Sales & Revenue" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                    <div class="m-4 text-center font-semibold">
                        <span>Sales & Revenue</span>
                    </div>
                </a>
                <a :href="userIsAdmin ? '/quotation' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/quotation.png" alt="Quotations" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                    <div class="m-4 text-center font-semibold">
                        <span>Quotations</span>
                    </div>
                </a>
                <a :href="userIsAdmin ? '/expenses' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/salary.png" alt="Expenses" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                    <div class="m-4 text-center font-semibold">
                        <span>Expenses</span>
                    </div>
                </a>
                <a :href="userIsAdmin ? '/govt_external' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/government.png" alt="Government Related" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                    <div class="m-4 text-center font-semibold">
                        <span>Government Related (External Affairs)</span>
                    </div>
                </a>
            </div>

            <slot></slot>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const isHomePage = computed(() => usePage().url === '/')
const isLoginPage = computed(() => usePage().url === '/login')
const showImages = computed(() => isHomePage.value)
const userIsAdmin = computed(() => {
    console.log('User data:', usePage().props.auth?.user)
    return usePage().props.auth?.user?.role === 'Admin'
})

const showBackButton = computed(() => !isHomePage.value && !isLoginPage.value);

//determine back button destination and tooltip
const backButtonLink = computed(() => {
    const currentPath = usePage().url;
    
    //if on a feature page, go back to company page
    if(currentPath.startsWith('/survey_monitoring') || 
        currentPath.startsWith('/dashboard') ||
        currentPath.startsWith('/sales_and_revenue') ||
        currentPath.startsWith('/quotation') ||
        currentPath.startsWith('/expenses') ||
        currentPath.startsWith('/office_equipment') ||
        currentPath.startsWith('/company_assets')
    ){
        return '/ggss';
    }
    //add similar conditions for GCO features if needed
    else if(currentPath.startsWith('/construction_dashboard') ||
        currentPath.startsWith('/construction_monitoring') ||
        currentPath.startsWith('/construction_revenue') ||
        currentPath.startsWith('/construction_expenses')
    ){
        return '/gco';
    }
    
    // Default to home if we're not sure
    return '/';
});


const backButtonTooltip = computed(() => {
    if (backButtonLink.value === '/ggss') return 'Back to GGSS';
    if (backButtonLink.value === '/gco') return 'Back to GCO';
    return 'Back to Home';
});

const currentUser = computed(() => usePage().props.auth?.user)
const userName = computed(() => currentUser.value?.username)
// const userAvatar = computed(() => {
//     const userId = currentUser.value?.id;
    
//     const avatarMap = {
//         3: '/images/kuya.png',
//         1: '/images/jen.png',
//         2: '/images/arnold.png',
//     };
//     return avatarMap[userId]
// });

const headerTitle = computed(() => {
    const url = usePage().url;
    if (url === '/ggss') return 'Geopete Geodetic Surveying Services';
    if (url === '/gco') return 'Geopete Constructions';
    return 'GGSS Monitoring System';
});

function logout() {
    axios.post('logout').then(() => {
        window.location.href = '/login'
    });
}
</script>
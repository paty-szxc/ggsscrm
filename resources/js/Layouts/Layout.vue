<template>
    <div>
        <header v-if="!isLoginPage" class="bg-gradient-to-b from-green-500 to-blue-500 text-white p-4 flex items-center justify-between">
            <h1 class="font-medium text-xl">GGSS Monitoring System</h1>
            <!-- <nav v-if="!isHomePage" class="flex space-x-6">
                <v-tooltip text="Go back to Home" location="bottom">
                    <template v-slot:activator="{ props }">
                        <a href="/" v-bind="props">
                            <v-icon>mdi-arrow-left-bold-box-outline</v-icon>
                        </a>
                    </template>
                </v-tooltip>
            </nav> -->
            <!-- <v-tooltip text="Logout" location="bottom">
                <template v-slot:activator="{ props }">
                    <button v-if="isHomePage" @click="logout" v-bind="props" class="flex items-center space-x-1 p-2 rounded">
                        <v-icon>mdi-logout</v-icon>
                        <span v-if="!isHomePage" class="hidden sm:inline">Logout</span>
                    </button>
                </template>
            </v-tooltip> -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <img 
                        :src="userAvatar" 
                        alt="User Avatar" 
                        class="w-8 h-8 rounded-full object-cover border-2 border-white"
                    >
                    <span class="hidden sm:inline">{{ userName }}</span>
                </div>

                <nav v-if="!isHomePage" class="flex space-x-6">
                    <v-tooltip text="Go back to Home" location="bottom">
                        <template v-slot:activator="{ props }">
                            <a href="/" v-bind="props">
                                <v-icon>mdi-arrow-left-bold-box-outline</v-icon>
                            </a>
                        </template>
                    </v-tooltip>
                </nav>

                <v-tooltip text="Logout" location="bottom">
                    <template v-slot:activator="{ props }">
                        <button v-if="isHomePage" @click="logout" v-bind="props" class="flex items-center space-x-1 p-2 rounded">
                            <v-icon>mdi-logout</v-icon>
                            <span v-if="!isHomePage" class="hidden sm:inline">Logout</span>
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

const currentUser = computed(() => usePage().props.auth?.user)
const userName = computed(() => currentUser.value?.username)
const userAvatar = computed(() => {
    const userId = currentUser.value?.id;
    
    const avatarMap = {
        3: '/images/kuya.png',
        1: '/images/jen.png',
        2: '/images/arnold.png',
    };
    return avatarMap[userId]
});

function logout() {
    axios.post('logout').then(() => {
        window.location.href = '/login'
    });
}
</script>

<style>
.disabled-link {
    pointer-events: none;
    cursor: not-allowed;
    text-decoration: none;
}
</style>
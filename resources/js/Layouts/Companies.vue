<template>
    <div>
        <header v-if="!isLoginPage" class="bg-gradient-to-b from-green-500 to-blue-500 text-white p-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-800">GGSS/GPC CRM</h1>
            <div class="flex items-center gap-4">
            <!-- <div class="relative">
                <img 
                src="/public/images/andrea.jpg" 
                alt="User Avatar" 
                class="w-8 h-8 rounded-full object-cover border-2 border-white"
                >
            </div> -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <!-- <img 
                            :src="userAvatar" 
                            alt="User Avatar" 
                            class="w-8 h-8 rounded-full object-cover border-2 border-white"
                        > -->
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
            </div>
        </header>

        <main>
            <div class="justify-center grid lg:grid-flow-col grid-rows-1 gap-12 px-16 md:py-16">
                <a @click.prevent="goToGgss" class="block cursor-pointer">
                    <v-tooltip 
                        text="Go to GGSS"
                        location="bottom">
                        <template v-slot:activator="{ props }">
                            <div v-bind="props">
                                <img 
                                    src="/public/images/logo.png" 
                                    alt="GGSS" 
                                    class="h-64 shadow-xl/30 -inset-2 rounded-lg opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                                <div class="m-4 text-center font-semibold">
                                    <span>Geopete Geodetic Surveying Services</span>
                                </div>
                            </div>
                        </template>
                    </v-tooltip>
                </a>
                <a @click.prevent="isAllowedUser ? goToGco() : null" 
                    :class="{ 'cursor-not-allowed opacity-50': !isAllowedUser }"
                    class="block cursor-pointer">
                    <v-tooltip 
                        :text="!isAllowedUser ? 'Access restricted to authorized users only.' : 'Go to Geopete Construction'" 
                        location="bottom">
                        <template v-slot:activator="{ props }">
                            <div v-bind="props">  <!-- Activator div wraps content -->
                                <img 
                                    src="/public/images/gpc.png" 
                                    alt="GPC" 
                                    class="h-64 shadow-xl/30 -inset-2 rounded-lg opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none"
                                    :class="{ 'hover:-translate-y-0': !isAllowedUser }">
                                <div class="m-4 text-center font-semibold">
                                    <span>Geopete Construction</span>
                                </div>
                            </div>
                        </template>
                    </v-tooltip>
                </a>
            </div>
    
            <slot></slot>
        </main>
    </div>
</template>

<script setup>
import axios from 'axios';
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const isHomePage = computed(() => usePage().url === '/')
const isLoginPage = computed(() => usePage().url === '/login')

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

const isAllowedUser = computed(() => {
    const userId = currentUser.value?.id;
    return [1, 3, 5, 6].includes(userId);
});

function logout() {
    axios.post('logout').then(() => {
        window.location.href = '/login'
    });
}

function goToGgss() {
    router.visit('/ggss');
}
function goToGco() {
    router.visit('/gco');
}
</script>
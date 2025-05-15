<template>
    <div>
        <header class="bg-gradient-to-b from-green-500 to-blue-500 text-white p-4 flex items-center justify-between">
            <h1 class="font-medium text-xl">GGSS Monitoring System</h1>
            <nav v-if="!isHomePage" class="flex space-x-6">
                <v-tooltip text="Go back to Home" location="bottom">
                    <template v-slot:activator="{ props }">
                        <a href="/" v-bind="props">
                            <v-icon>mdi-arrow-left-bold-box-outline</v-icon>
                        </a>
                    </template>
                </v-tooltip>
            </nav>
        </header>

        <main>
            <div v-if="showImages" class="justify-center grid lg:grid-flow-col grid-rows-1 gap-12  px-16 md:py-16">
                <a href="/dashboard" class="block cursor-pointer">
                    <img src="/public/images/dashboard.png" alt="Dashboard" class="h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <div class="m-4 text-center font-semibold">
                        <span>Dashboard</span>
                    </div>
                </a>
                <a href="/survey_monitoring" class="block cursor-pointer">
                    <img src="/public/images/task-management.png" alt="Dashboard" class="h-64 shadow-xl/30  -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <div class="m-4 text-center font-semibold">
                        <span>Project Monitoring</span>
                    </div>
                </a>
                <a href="/sales_and_revenue" class="block cursor-pointer">
                    <img src="/public/images/revenue.png" alt="Dashboard" class="h-64 shadow-xl/30  -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <div class="m-4 text-center font-semibold">
                        <span>Sales & Revenue</span>
                    </div>
                </a>
                <a href="/quotation" class="block cursor-pointer">
                    <img src="/public/images/quotation.png" alt="Dashboard" class="h-64 shadow-xl/30  -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <div class="m-4 text-center font-semibold">
                        <span>Quotations</span>
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

const isHomePage = computed(() => usePage().url === '/');
const showImages = computed(() => isHomePage.value);
</script>
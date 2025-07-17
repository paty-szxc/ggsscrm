<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2 sm:px-4 md:px-8 lg:px-16 py-4 md:py-16 justify-center">
        <a :href="userIsAdmin ? '/construction_dashboard' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                        <img src="/public/images/construction-dashboard.png" alt="Dashboard" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                        <div class="m-4 font-semibold text-center">
                            <span>Dashboard</span>
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/construction-dashboard.png" alt="Dashboard" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                </div>
                <div class="m-4 font-semibold text-center">
                    <span>Dashboard</span>
                </div>
            </template>
        </a>
        <a href="construction_monitoring" class="block cursor-pointer flex flex-col items-center">
            <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/project-management.png" alt="Project Monitoring" class="h-60 shadow-xl/30 -inset-2 rounded-lg">
            </div>
            <div class="m-4 font-semibold text-center">
                <span>Project Monitoring</span>
            </div>
        </a>
        <a :href="userIsAdmin ? '/construction_revenue' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                            <img src="/public/images/construction_revenue.png" alt="Sales & Revenue" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        </div>
                        <div class="m-4 font-semibold text-center">
                            <span>Sales & Revenue</span>
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                <img src="/public/images/revenue.png" alt="Sales & Revenue" 
                    :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                </div>
                <div class="m-4 font-semibold text-center">
                    <span>Sales & Revenue</span>
                </div>
            </template>
        </a>
        <a :href="userIsAdmin ? '/quotation' : null" 
            :class="{'flex flex-col items-center': true, 'cursor-not-allowed opacity-20': userIsAdmin, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                text="Still Ongoing"
                location="bottom">
                <template v-slot:activator="{ props }">
                <div v-bind="props">
                    <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img 
                        src="/public/images/quote.png" 
                        alt="Quotations" 
                        :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-20': userIsAdmin, 'opacity-20': !userIsAdmin}">
                    </div>
                    <div class="m-4 font-semibold text-center">
                    <span>Quotations</span>
                    </div>
                </div>
                </template>
            </v-tooltip>
        </a>
        <a :href="userIsAdmin ? '/construction_expenses' : null" 
            :class="{'cursor-pointer flex flex-col items-center': true, 'cursor-not-allowed opacity-50': !userIsAdmin}">
            <v-tooltip
                v-if="!userIsAdmin"
                text="Access restricted to authorized users only."
                location="bottom">
                <template v-slot:activator="{ props }">
                    <div v-bind="props">
                        <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                        <img src="/public/images/construction-budget.png" alt="Expenses" 
                            :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-20': !userIsAdmin}">
                        </div>
                        <div class="m-4 font-semibold text-center">
                            <span>Expenses</span>
                        </div>
                    </div>
                </template>
            </v-tooltip>
            <template v-if="userIsAdmin">
                <div class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <img src="/public/images/construction-budget.png" alt="Expenses" 
                        :class="{'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true, 'opacity-75': userIsAdmin, 'opacity-20': !userIsAdmin}">
                </div>
                <div class="m-4 font-semibold text-center">
                    <span>Expenses</span>
                </div>
            </template>
        </a>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// const isHomePage = computed(() => usePage().url === '/')
// const showImages = computed(() => isHomePage.value)
const userIsAdmin = computed(() => {
    const userId = usePage().props.auth?.user?.id;
    console.log('User  data:', userId);
    return [1, 3, 4, 5, 6].includes(userId);
});

</script>
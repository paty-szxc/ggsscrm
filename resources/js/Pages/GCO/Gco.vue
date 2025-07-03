<template>
    <div class="justify-center grid lg:grid-flow-col grid-rows-1 gap-12 px-16 md:py-16">
        <a :href="userIsAdmin ? '/construction_dashboard' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/construction-dashboard.png" alt="Dashboard" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                        <div class="m-4 text-center font-semibold">
                        <span>Dashboard</span>
                    </div>
                </a>
                <a href="construction_monitoring" class="block cursor-pointer">
                    <img src="/public/images/project-management.png" alt="Project Monitoring" class="h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 opacity-75 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none">
                    <div class="m-4 text-center font-semibold">
                        <span>Project Monitoring</span>
                    </div>
                </a>
                <a :href="userIsAdmin ? '/construction_revenue' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/construction_revenue.png" alt="Sales & Revenue" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                    <div class="m-4 text-center font-semibold">
                        <span>Sales & Revenue</span>
                    </div>
                </a>
                <a :href="userIsAdmin ? '/quotation' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/quote.png" alt="Quotations" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                    <div class="m-4 text-center font-semibold">
                        <span>Quotations</span>
                    </div>
                </a>
                <a :href="userIsAdmin ? '/construction_expenses' : null" 
                    :class="{'block cursor-pointer': true, 'disabled-link': !userIsAdmin}">
                        <img src="/public/images/construction-budget.png" alt="Expenses" 
                            :class="{'h-64 shadow-xl/30 -inset-2 rounded-lg bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none': true, 'opacity-75': userIsAdmin, 'opacity-50': !userIsAdmin}">
                    <div class="m-4 text-center font-semibold">
                        <span>Expenses</span>
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
    const userId = usePage().props.auth?.user?.id;
    console.log('User  data:', userId);
    return [1, 3].includes(userId);
});

</script>
<template>
    <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 
        lg:grid-cols-4 gap-6 px-2 sm:px-4 md:px-8 
        lg:px-16 py-4 md:py-16 justify-center">
        <DashboardLink
            v-for="link in links"
            :key="link.title"
            :title="link.title"
            :link="link.link"
            :image-src="link.imageSrc"
            :has-access="link.hasAccess"
            :tooltip-text="link.tooltipText"/>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import DashboardLink from '../../Components/DashboardLink.vue';

const userIsAdmin = computed(() => {
    const userRole = usePage().props.auth?.user?.role
    return ['Admin'].includes(userRole)
})

const userIsViewer = computed(() => {
    const authUser = usePage().props.auth?.user
    if (!authUser) return false
    const userRole = authUser.role
    const userEmpCode = authUser.emp_code
    const allowedViewerEmpCodes = ['0000-0002', '0000-0004', '0001-0000']
    return (
        ['Encoder & Viewer', 'Encoder'].includes(userRole) &&
        allowedViewerEmpCodes.includes(userEmpCode)
    )
})

const links = computed(() => [
    {
        title: 'Dashboard',
        link: '/construction_dashboard',
        imageSrc: '/images/construction-dashboard.png',
        hasAccess: userIsAdmin.value,
    },
    {
        title: 'Project Monitoring',
        link: '/construction_monitoring',
        imageSrc: '/images/project-management.png',
        hasAccess: true, // Always accessible based on original code
    },
    {
        title: 'Sales & Revenue',
        link: '/construction_revenue',
        imageSrc: '/images/construction_revenue.png',
        hasAccess: userIsAdmin.value,
    },
    {
        title: 'Quotations',
        link: '/construction_quotation',
        imageSrc: '/images/quote.png',
        hasAccess: userIsAdmin.value || userIsViewer.value,
    },
    {
        title: 'Expenses',
        link: '/construction_expenses',
        imageSrc: '/images/construction-budget.png',
        hasAccess: userIsAdmin.value,
    },
])
</script>

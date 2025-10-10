<template>
    <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 
            lg:grid-cols-5 gap-6 px-2 sm:px-4 md:px-8 
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
    if(!authUser) return false
    const userRole = authUser.role
    const userEmpCode = authUser.emp_code
    const allowedViewerEmpCodes = ['0000-0003', '0001-0000']
    return(
        ['Encoder & Viewer', 'Encoder'].includes(userRole) &&
        allowedViewerEmpCodes.includes(userEmpCode)
    )
})

const userIsViewer1 = computed(() => {
    const authUser = usePage().props.auth?.user
    if(!authUser) return false
    const userRole = authUser.role
    const userEmpCode = authUser.emp_code
    const allowedViewerEmpCodes = ['0000-0004', '0000-0016']
    return(
        ['Encoder & Viewer', 'Encoder'].includes(userRole) &&
        allowedViewerEmpCodes.includes(userEmpCode)
    )
})

const userGovtViewer = computed(() => {
    const authUser = usePage().props.auth?.user
    if(!authUser) return false
    const userEmpCode = authUser.emp_code
    const allowedViewerEmpCodes = ['0000-0004', '0000-0016']
    return allowedViewerEmpCodes.includes(userEmpCode)
})

const userIsChecker = computed(() => {
    const authUser = usePage().props.auth?.user
    if(!authUser) return false
    const userEmpCode = authUser.emp_code
    const checkerEmpCode = '0000-0011'
    return userEmpCode === checkerEmpCode
})

const userNoAccess = computed(() => {
    const authUser = usePage().props.auth?.user
    if(!authUser) return false
    const userEmpCode = authUser.emp_code
    const checkerEmpCode = '0000-0004'
    return userEmpCode === checkerEmpCode
})

const links = computed(() => [
    {
        title: 'Dashboard',
        link: '/dashboard',
        imageSrc: '/images/dashboard.png',
        hasAccess: userIsAdmin.value,
    },
    {
        title: 'Project Monitorting',
        link: '/survey_monitoring',
        imageSrc: '/images/task-management.png',
        hasAccess: !userIsChecker.value,
    },
    {
        title: 'Sales & Revenue',
        link: '/sales_and_revenue',
        imageSrc: '/images/revenue.png',
        hasAccess: userIsAdmin.value,
    },
    {
        title: 'Quotations',
        link: '/survey_quotation',
        imageSrc: '/images/quotation.png',
        hasAccess: userIsAdmin.value || userIsViewer.value,
    },
    {
        title: 'Expenses',
        link: '/expenses',
        imageSrc: '/images/salary.png',
        hasAccess: userIsAdmin.value,
    },
    {
        title: 'Office Equipments',
        link: '/office_equipment',
        imageSrc: '/images/office.png',
        hasAccess: userIsAdmin.value || userIsViewer.value,
    },
    {
        title: 'Survey Equipments',
        link: '/survey_equipments',
        imageSrc: '/images/equipment.png',
        hasAccess: !userNoAccess.value,
    },
    {
        title: 'Company Assets',
        link: '/company_assets',
        imageSrc: '/images/asset.png',
        hasAccess: userIsAdmin.value || userIsViewer.value || userIsViewer1.value,
    },
    {
        title: 'Government Related (External Affairs)',
        link: '/govt_external',
        imageSrc: '/images/government.png',
        hasAccess: userIsAdmin.value || userGovtViewer.value,
    },
    {
        title: 'Titles',
        link: '/titles',
        imageSrc: '/images/land-titles.png',
        hasAccess: !userIsChecker.value,
    },
])
</script>

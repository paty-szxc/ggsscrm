<template>
    <v-app>
    <v-app-bar app>
        <v-toolbar-title>GGSS Monitoring System</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click="drawer = !drawer">
        <v-icon>mdi-menu</v-icon>
        </v-btn>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app>
        <v-list>
        <v-list-item @click="navigateTo('ProjectMonitoring')">
            <v-list-item-title>Project Monitoring</v-list-item-title>
        </v-list-item>
        <v-list-item @click="navigateTo('SalesRevenue')">
            <v-list-item-title>Sales & Revenue</v-list-item-title>
        </v-list-item>
        <v-list-item @click="navigateTo('Dashboard')">
            <v-list-item-title>Dashboard</v-list-item-title>
        </v-list-item>
        </v-list>
    </v-navigation-drawer>

    <v-main>
        <inertia-app :initial-page="initialPage" :resolve-component="resolveComponent" />
    </v-main>
    </v-app>
</template>

<script setup>
import { ref } from 'vue';

// State for the drawer
const drawer = ref(false);

// Props
const props = defineProps({
    initialPage: Object,
    resolveComponent: Function,
});

// Navigation method
const navigateTo = (page) => {
    Inertia.visit(`/${page}`);
    drawer.value = false; // Close the drawer after navigation
};
</script>

<style>
/* Add any custom styles here */
</style>

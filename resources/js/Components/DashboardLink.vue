<template>
    <a
        :href="hasAccess ? link : null"
        :class="{
        'cursor-pointer flex flex-col items-center': true,
        'cursor-not-allowed opacity-50': !hasAccess,
        }"
    >
        <v-tooltip v-if="!hasAccess" :text="tooltipText" location="bottom">
        <template v-slot:activator="{ props }">
            <div v-bind="props">
            <div
                class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none"
            >
                <img
                :src="imageSrc"
                :alt="title"
                :class="{
                    'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true,
                    'opacity-75': hasAccess,
                    'opacity-50': !hasAccess,
                }"
                />
            </div>
            </div>
        </template>
        </v-tooltip>

        <template v-if="hasAccess">
        <div
            class="w-64 h-64 rounded-lg flex items-center justify-center bg-gradient-to-r from-blue-600 via-green-600 to-blue-600 transform transition hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:transform-none"
        >
            <img
            :src="imageSrc"
            :alt="title"
            :class="{
                'h-60 shadow-xl/30 -inset-2 rounded-lg object-contain': true,
                'opacity-75': hasAccess,
                'opacity-50': !hasAccess,
            }"
            />
        </div>
        </template>

        <div
        class="m-4 font-semibold text-center min-h-[3.5rem] flex items-center justify-center"
        >
        <span class="leading-tight">{{ title }}</span>
        </div>
    </a>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    link: {
        type: String,
        required: true,
    },
    imageSrc: {
        type: String,
        required: true,
    },
    hasAccess: {
        type: Boolean,
        default: true,
    },
    tooltipText: {
        type: String,
        default: 'Access restricted to authorized users only.',
    },
});
</script>

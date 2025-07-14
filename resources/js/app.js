import '../css/app.css'
import '../css/style.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createVuetify } from 'vuetify';
import 'vuetify/styles'
import Layout from './Layouts/Layout.vue'
import '@mdi/font/css/materialdesignicons.css';

import { VTextField } from 'vuetify/components/VTextField'
import { VDateInput } from 'vuetify/labs/VDateInput'

const vuetify = createVuetify({
    components: {
        VTextField,
        VDateInput,
    },
    defaults: {
        VTextField: {
            variant: 'outlined',
            density: 'compact',
        },
    },
});

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page =  pages[`./Pages/${name}.vue`]
        page.default.layout = page.default.layout || Layout
        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(vuetify)
        .mount(el)
    },
})
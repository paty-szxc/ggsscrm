import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import '@mdi/font/css/materialdesignicons.css';

import { VTextField } from 'vuetify/components/VTextField'
import { VDateInput } from 'vuetify/labs/VDateInput'

const vuetify = createVuetify({
    components: {
        VTextField,
        VDateInput
    },

    defaults: {
        VTextField: {
            variant: 'outlined',
            density: 'compact',
        }
    },
    theme: {
        themes: {
            light: {
            // Define your light theme colors here
            },
            dark: {
                // Define your dark theme colors here
            },
        },
    },
})

export default vuetify;
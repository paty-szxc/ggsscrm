<template>
    <div class="min-h-screen content-center p-4">
        <div class="w-full max-w-4xl mx-auto">
            <div class="flex flex-col md:flex-row rounded-xl shadow-lg overflow-hidden bg-white">
                <div class="md:w-1/2 h-64 md:h-auto bg-[url('/images/login.jpg')] bg-cover bg-center">
                </div>
                <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-12">
                    <div class="w-full max-w-xs mx-auto">
                        <template v-if="isLogin">
                            <h2 class="text-2xl md:text-3xl font-bold mb-4 text-center">Login</h2>
                            <p class="text-gray-600 mb-6 text-center">Welcome back!</p>
                            <v-form @submit.prevent="handleLogin" ref="loginForm">
                                <div class="space-y-4">
                                    <v-text-field
                                        class="w-full"
                                        density="compact"
                                        label="Username"
                                        variant="outlined"
                                        v-model="newUser.username">
                                    </v-text-field>
                                    <v-text-field
                                        class="w-full"
                                        density="compact"
                                        label="Password"
                                        type="password"
                                        variant="outlined"
                                        v-model="newUser.password">
                                    </v-text-field>
                                    <div class="flex justify-between items-center">
                                    <v-checkbox
                                        label="Remember me"
                                        density="compact"
                                        hide-details
                                        v-model="newUser.remember"
                                    ></v-checkbox>
                                    <!-- <a href="#" class="text-sm text-blue-600 hover:underline" @click.prevent="showForgotPassword">
                                        Forgot password?
                                    </a> -->
                                    </div>
                                    <v-btn 
                                        block 
                                        class="bg-purple-600 text-white mt-2"
                                        @click="loginBtn"
                                        :loading="loading">
                                        Login
                                    </v-btn>
                                    <div class="text-center mt-4">
                                        <a href="#" @click.prevent="toggleForm" class="text-blue-600 hover:underline">
                                            Don't have an account? Register now!
                                        </a>
                                    </div>
                                </div>
                            </v-form>
                        </template>
                        <template v-else>
                            <h2 class="text-2xl md:text-3xl font-bold mb-4 text-center">Register</h2>
                            <p class="text-gray-600 mb-6 text-center">Create your account.</p>
                            <v-form @submit.prevent="handleRegister" ref="registerForm">
                                <div class="space-y-4">
                                    <v-text-field
                                        class="w-full"
                                        density="compact"
                                        label="Employee Code"
                                        placeholder="0000-0000"
                                        :rules="empCodeRules"
                                        variant="outlined"
                                        v-model="newUser.emp_code">
                                    </v-text-field>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <v-text-field
                                        density="compact"
                                        label="Firstname"
                                        variant="outlined"
                                        v-model="newUser.first_name">
                                    </v-text-field>
                                    <v-text-field
                                        density="compact"
                                        label="Surname"
                                        variant="outlined"
                                        v-model="newUser.surname">
                                    </v-text-field>
                                    </div>
                                    <v-text-field
                                        density="compact"
                                        label="Username"
                                        variant="outlined"
                                        v-model="newUser.username">
                                    </v-text-field>
                                    <v-text-field
                                        :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                        class="w-full"
                                        @click:append-inner="showPassword = !showPassword"
                                        density="compact"
                                        label="Password"
                                        :type="showPassword ? 'text' : 'password'"
                                        variant="outlined"
                                        :rules="passwordRules"
                                        v-model="newUser.password"
                                        >
                                    </v-text-field>
                                    <v-text-field
                                        :append-inner-icon="showConfirmPass ? 'mdi-eye-off' : 'mdi-eye'"
                                        class="w-full"
                                        @click:append-inner="showConfirmPass = !showConfirmPass"
                                        density="compact"
                                        label="Confirm Password"
                                        :type="showConfirmPass ? 'text' : 'password'"
                                        :rules="confirmPassRules"
                                        variant="outlined"
                                        v-model="newUser.confirmPass">
                                    </v-text-field>
                                    <v-autocomplete
                                        class="w-full"
                                        density="compact"
                                        :items="roles"
                                        label="Role"
                                        :rules="roleRules"
                                        variant="outlined"
                                        v-model="newUser.role">
                                    </v-autocomplete>
                                    <v-btn 
                                        block 
                                        class="bg-purple-600 text-white mt-2"
                                        @click="registerBtn"
                                        :loading="loading">
                                        Register
                                    </v-btn>
                                    <div class="text-center mt-4">
                                    <a href="#" @click.prevent="toggleForm" class="text-blue-600 hover:underline">
                                        Already have an account? Login!
                                    </a>
                                    </div>
                                </div>
                            </v-form>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <Snackbar ref="snackbar"></Snackbar>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import Snackbar from '../Components/Snackbar.vue';

const loading = ref(false)
watch(loading, val => {
    if(!val) return
    setTimeout(() => (loading.value = false), 2000)
})

const isLogin = ref(false)
const loginForm = ref(null)
const registerForm = ref(null)
const newUser = ref({})
const showPassword = ref(false)
const showConfirmPass = ref(false)
const roles = ['Admin', 'Encoder', 'Viewer', 'Encoder & Viewer', 'Checker']
const snackbar = ref(null)

const empCodeRules = [
    (v) => !!v || 'Employee Code is required.',
    (v) => (v && v.length >= 9) || 'Employee Code must be 9 characters',
]

const roleRules = [
    (v) => !!v || 'Role is required.'
]

const passwordRules = [
    (v) => !!v || 'Password is required.',
    (v) => (v && v.length >= 8) || 'Password must be at least 8 characters',
]

const confirmPassRules = computed(() => [
    (v) => !!v || 'Please confirm your password.',
    (v) => v === newUser.value.password || 'Passwords do not match',
])

const loginBtn = async () => {
    loading.value = true
    const { valid } = await loginForm.value.validate()

    if (!valid) {
        loading.value = false
        return
    }

    try {
        await axios.get('/sanctum/csrf-cookie')
        const res = await axios.post('login', newUser.value)
        console.log(res)
        newUser.value = {}
        window.location.href = '/' // Full page reload
        // OR for SPA behavior:
        // router.visit('/')
    } catch (err) {
        console.error('Login failed:', err)
    } finally {
        loading.value = false
    }
}

const registerBtn = async () => {
    loading.value = true
    const { valid } = await registerForm.value.validate()
    
    if(!valid){
        loading.value = false
        return
    }

    try{
        await axios.get('sanctum/csrf-cookie')
        
        const payload = {
            emp_code: newUser.value.emp_code,
            first_name: newUser.value.first_name,
            surname: newUser.value.surname,
            username: newUser.value.username,
            password: newUser.value.password,
            password_confirmation: newUser.value.confirmPass,
            role: newUser.value.role,
        }

        const response = await axios.post('register', payload)
        
        if (response.data.message) {
            snackbar.value.alertCustom('Registration Successful!')
            newUser.value = {}
        }
    }
    catch(err){
        let errorMessage = 'Error registering user';
        if (err.response?.data?.errors){
            errorMessage = Object.values(err.response.data.errors)
            .flat()
            .join(' ')
        } 
        else if(err.response?.data?.message) {
            errorMessage = err.response.data.message
        }
        
        snackbar.value.alertCustom(errorMessage, 'error')
        console.error('Registration error:', err)
    } 
    finally{
        loading.value = false
    }
};

const handleLogin = async () => {
    const { valid } = await loginForm.value.validate()
    if(!valid) return
    try{
        console.log('Logging in...')
    } 
    finally{
    }
}

const handleRegister = async () => {
    const { valid } = await registerForm.value.validate()
    if(!valid) return
    try{
        console.log('Registering...')
    }
    finally{
    }
}

const toggleForm = () => {
    isLogin.value = !isLogin.value
}

const showForgotPassword = () => {
    if(newUser.value.password){
        snackbar.value.alertCustom(`Your password is: ${newUser.value.password}`, 'info');
    } 
    else{
        snackbar.value.alertCustom('Please enter your password first.', 'warning');
    }
};
</script>
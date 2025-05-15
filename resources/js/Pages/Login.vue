<template>
    <v-container class="w-full max-w-sm mx-auto">
        <v-sheet>
            <v-card>
                <v-card-text class="px-6 py-4">
                    <div class="flex justify-center mx-auto">
                        <v-img
                            src="images/logo.png"
                            class="w-auto h-32"
                            alt="Logo"
                        ></v-img>
                    </div>
    
                    <p class="text-h5">LOGIN</p>
    
                    <v-form>
                        <v-text-field
                            class="mt-4"
                            density="compact"
                            label="Username"
                            type="email"
                            aria-label="Username"
                            variant="outlined"
                        ></v-text-field>
    
                        <v-text-field
                            class="mt-4"
                            density="compact"
                            label="Password"
                            type="password"
                            aria-label="Password"
                            variant="outlined"
                        ></v-text-field>
    
                        <div class="flex items-center justify-between mt-4">
                            <v-btn variant="text" class="text-sm text-gray-600 dark:text-gray-200 hover:text-gray-500">
                                Forget Password?
                            </v-btn>
    
                            <v-btn
                                class="px-6 py-2 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-blue-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                                @click="signIn"
                            >
                                Sign In
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
    
                <v-divider></v-divider>
    
                <v-card-actions class="flex items-center justify-center py-4 text-center bg-gray-50 dark:bg-gray-700">
                    <span class="text-sm text-gray-600 dark:text-gray-200">Don't have an account? </span>
                    <v-btn text class="mx-2 text-sm font-bold text-blue-500 dark:text-blue-400 hover:underline">
                        Register
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-sheet>
    </v-container>
</template>

<script setup>
const signIn = () => {
    // Handle sign-in logic here
};
</script>

<style scoped>
/* You can add any additional styles here if needed */
</style>
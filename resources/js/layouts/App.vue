<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <RouterLink to="/" class="flex items-center px-4 text-gray-900 font-semibold text-lg">
                            Product Admin
                        </RouterLink>
                        <div class="hidden sm:flex sm:space-x-8">
                            <RouterLink to="/" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 border-b-2" active-class="border-indigo-500 text-indigo-600" inactive-class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Home
                            </RouterLink>
                            <RouterLink to="/products" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 border-b-2" active-class="border-indigo-500 text-indigo-600" inactive-class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Products
                            </RouterLink>
                            <RouterLink to="/users" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 border-b-2" active-class="border-indigo-500 text-indigo-600" inactive-class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Users
                            </RouterLink>
                        </div>
                    </div>
                    <div class="flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg v-if="!mobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <svg v-else class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="sm:hidden" id="mobile-menu" v-show="mobileMenuOpen">
                <div class="pt-2 pb-3 space-y-1">
                    <RouterLink to="/" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-indigo-50 border-indigo-500 text-indigo-700" inactive-class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        Home
                    </RouterLink>
                    <RouterLink to="/products" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-indigo-50 border-indigo-500 text-indigo-700" inactive-class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        Products
                    </RouterLink>
                    <RouterLink to="/users" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-indigo-50 border-indigo-500 text-indigo-700" inactive-class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        Users
                    </RouterLink>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <RouterView />
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const mobileMenuOpen = ref(false)
</script>

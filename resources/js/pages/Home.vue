<template>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Products</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ productCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-3.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ userCount }}</p>
                    </div>
                </div>
            </div>

            <div v-if="auth.hasRole('Admin')" class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Accounts</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ accountCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Links</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <RouterLink to="/products" class="block bg-indigo-50 hover:bg-indigo-100 rounded-lg p-4 text-center transition">
                    <span class="text-base font-medium text-indigo-700">Products</span>
                </RouterLink>
                <RouterLink to="/users" class="block bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center transition">
                    <span class="text-base font-medium text-green-700">Users</span>
                </RouterLink>
                <RouterLink v-if="auth.hasRole('Admin')" to="/accounts" class="block bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center transition">
                    <span class="text-base font-medium text-purple-700">Accounts</span>
                </RouterLink>
                <RouterLink to="/profile" class="block bg-slate-50 hover:bg-slate-100 rounded-lg p-4 text-center transition">
                    <span class="text-base font-medium text-slate-700">Profile</span>
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuth } from '../composables/useAuth'

const auth = useAuth()
const productCount = ref(0)
const userCount = ref(0)
const accountCount = ref(0)

const fetchCount = async (url) => {
    const response = await fetch(url, {
        headers: { 'Accept': 'application/json' },
    })
    if (!response.ok) {
        return 0
    }
    const data = await response.json()
    return data?.meta?.total ?? 0
}

onMounted(async () => {
    const [p, u, a] = await Promise.all([
        fetchCount('/api/products?page=1'),
        fetchCount('/api/users?page=1'),
        fetchCount('/api/accounts?page=1'),
    ])
    productCount.value = p
    userCount.value = u
    accountCount.value = a
})
</script>
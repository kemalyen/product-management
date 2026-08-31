<template>
    <div class="min-h-screen bg-gray-50">
        <nav v-if="auth.isAuthenticated" class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <RouterLink to="/" class="flex items-center px-4 text-gray-900 font-semibold text-lg">
                            Product Admin
                        </RouterLink>
                        <div class="hidden sm:flex sm:space-x-8">
                            <RouterLink to="/" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-600" inactive-class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Home
                            </RouterLink>
                            <RouterLink to="/products" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-600" inactive-class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Products
                            </RouterLink>
                            <RouterLink to="/users" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-600" inactive-class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Users
                            </RouterLink>
                            <RouterLink to="/profile" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-600" inactive-class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Profile
                            </RouterLink>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <button @click="handleLogout" type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign out
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
                    <RouterLink to="/profile" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-indigo-50 border-indigo-500 text-indigo-700" inactive-class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        Profile
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const auth = useAuth()
const mobileMenuOpen = ref(false)

onMounted(async () => {
    await auth.fetchUser()
})

const handleLogout = async () => {
    await auth.logout()
    router.push('/login')
}
</script>

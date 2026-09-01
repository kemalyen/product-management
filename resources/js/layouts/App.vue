<template>
    <div class="min-h-screen bg-gray-50">
        <nav v-if="auth.isAuthenticated" class="bg-slate-400 shadow-sm border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <RouterLink to="/" class="text-white font-semibold text-lg">
                            Product Admin
                        </RouterLink>
                    </div>
                    <div class="flex items-center space-x-8">
                        <RouterLink to="/" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:border-slate-300 hover:text-white">
                            Home
                        </RouterLink>
                        <RouterLink to="/products" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:border-slate-300 hover:text-white">
                            Products
                        </RouterLink>
                        <RouterLink to="/users" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:border-slate-300 hover:text-white">
                            Users
                        </RouterLink>
                        <RouterLink v-if="auth.hasRole('Admin')" to="/accounts" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:border-slate-300 hover:text-white">
                            Accounts
                        </RouterLink>
                        <RouterLink to="/profile" class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2" active-class="border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:border-slate-300 hover:text-white">
                            Profile
                        </RouterLink>
                        <button @click="handleLogout" type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-900 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign out
                        </button>
                    </div>
                </div>
            </div>

            <div class="sm:hidden" id="mobile-menu" v-show="mobileMenuOpen">
                <div class="pt-2 pb-3 space-y-1">
                    <RouterLink to="/" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-slate-800 border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:bg-slate-800 hover:border-slate-300 hover:text-white">
                        Home
                    </RouterLink>
                    <RouterLink to="/products" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-slate-800 border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:bg-slate-800 hover:border-slate-300 hover:text-white">
                        Products
                    </RouterLink>
                    <RouterLink to="/users" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-slate-800 border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:bg-slate-800 hover:border-slate-300 hover:text-white">
                        Users
                    </RouterLink>
                    <RouterLink to="/profile" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-slate-800 border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:bg-slate-800 hover:border-slate-300 hover:text-white">
                        Profile
                    </RouterLink>
                    <RouterLink v-if="auth.hasRole('Admin')" to="/accounts" class="block pl-3 pr-4 py-2 text-base font-medium border-l-4" active-class="bg-slate-800 border-indigo-500 text-indigo-400" inactive-class="border-transparent text-slate-300 hover:bg-slate-800 hover:border-slate-300 hover:text-white">
                        Accounts
                    </RouterLink>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <RouterView />
        </main>

        <ToastContainer />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import ToastContainer from '../components/ToastContainer.vue'

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

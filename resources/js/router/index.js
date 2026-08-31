import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Products from '../pages/Products.vue'
import Users from '../pages/Users.vue'
import Login from '../pages/Login.vue'
import Profile from '../pages/Profile.vue'
import Accounts from '../pages/Accounts.vue'
import { useAuth } from '../composables/useAuth'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { requiresGuest: true },
    },
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: { requiresAuth: true },
    },
    {
        path: '/products',
        name: 'products',
        component: Products,
        meta: { requiresAuth: true },
    },
    {
        path: '/users',
        name: 'users',
        component: Users,
        meta: { requiresAuth: true },
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true },
    },
    {
        path: '/accounts',
        name: 'accounts',
        component: Accounts,
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/',
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

let authInitialized = false

router.beforeEach(async (to) => {
    const auth = useAuth()

    if (!authInitialized) {
        authInitialized = true
        await auth.fetchUser()
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated.value) {
        return { name: 'login', query: { redirect: to.fullPath } }
    }

    if (to.meta.requiresAdmin && !auth.hasRole('Admin')) {
        return { name: 'home' }
    }

    if (to.meta.requiresGuest && auth.isAuthenticated.value) {
        return { name: 'home' }
    }

    return true
})

export default router

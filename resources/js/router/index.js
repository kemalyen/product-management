import { createRouter, createWebHistory } from 'vue-router'
import Products from '../pages/Products.vue'
import Users from '../pages/Users.vue'
import Home from '../pages/Home.vue'

const routes = [
    { path: '/', component: Home },
    { path: '/products', component: Products },
    { path: '/users', component: Users },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router

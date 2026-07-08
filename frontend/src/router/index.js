import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/pages/auth/Register.vue'
import Login from '@/pages/auth/Login.vue'
import PublicLayout from '@/layout/PublicLayout.vue'
import DashboardBacker from '@/pages/backer/Dashboard.vue'

const routes = [
    {
        path: '/register',
        name: 'register',
        component: Register
    },

    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/',
        component: PublicLayout,
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: DashboardBacker
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

export default router

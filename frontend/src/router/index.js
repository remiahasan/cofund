import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/pages/auth/Register.vue'
import Login from '@/pages/auth/Login.vue'
import PublicLayout from '@/layout/PublicLayout.vue'
import DashboardBacker from '@/pages/backer/Dashboard.vue'

const routes = [
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta:{ requiresAuth: false }
    },

    {
        path: '/login',
        name: 'login',
        component: Login,
        meta:{ requiresAuth: false }
    },
    {
        path: '/',
        component: PublicLayout,
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: DashboardBacker,
                meta:{ requiresAuth: true }
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const guestOnlyPages = ['register', 'login']
    
    if (to.meta.requiresAuth && !token) {
        next({ name: 'login' })
    } else if (guestOnlyPages.includes(to.name) && token) {
        next({ name: 'dashboard' })
    } else {
        next()
    }
})

export default router

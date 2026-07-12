import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/pages/auth/Register.vue'
import Login from '@/pages/auth/Login.vue'
import GuestLayout from '@/layout/GuestLayout.vue'
import PublicLayout from '@/layout/PublicLayout.vue'
import CampaignList from '@/pages/campaign/CampaignList.vue'
import CampaignDetail from '@/pages/campaign/CampaignDetail.vue'
import DashboardBacker from '@/pages/backer/Dashboard.vue'
import DashboardAdmin from '@/pages/admin/Dashboard.vue'
import DashboardCreator from '@/pages/creator/Dashboard.vue'
import CampaignForm from '@/pages/creator/CampaignForm.vue'
import CampaignEditForm from '@/pages/creator/CampaignEditForm.vue'
import Saldo from '@/pages/shared/Saldo.vue'

export const dashboardRouteByRole = {
    admin: 'dashboard.admin',
    backer: 'dashboard.backer',
    creator: 'dashboard.creator'
}

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
        component: GuestLayout,
        children: [
            {
                path: '',
                name: 'campaign.list',
                component: CampaignList,
                meta: { requiresAuth: false }
            },
            {
                path: 'campaigns/:id',
                name: 'campaign.detail',
                component: CampaignDetail,
                meta: { requiresAuth: false }
            }
        ]
    },
    {
        path: '/dashboard',
        component: PublicLayout,
        children: [
            {
                path: 'backer',
                name: 'dashboard.backer',
                component: DashboardBacker,
                meta: { requiresAuth: true, roles: ['backer'] }
            },
            {
                path: 'creator',
                name: 'dashboard.creator',
                component: DashboardCreator,
                meta: { requiresAuth: true, roles: ['creator'] }
            },
            {
                path: 'admin',
                name: 'dashboard.admin',
                component: DashboardAdmin,
                meta: { requiresAuth: true, roles: ['admin'] }
            },
            {
                path: 'creator/campaign/create',
                name: 'creator.campaign.create',
                component: CampaignForm,
                meta: { requiresAuth: true, roles: ['creator'] }
            },
            {
                path: 'creator/campaigns/:id/edit',
                name: 'campaign.edit',
                component: CampaignEditForm,
                meta: { requiresAuth: true, roles: ['creator'] }
            },
            {
                path: 'saldo',
                name: 'wallet',
                component: Saldo,
                meta: { requiresAuth: true }
            },
        ]
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const user = JSON.parse(localStorage.getItem('user') || 'null')
    const guestOnlyPages = ['login', 'register']

    if (to.meta.requiresAuth && !token) {
        next({ name: 'login' })
        return
    }
    if (to.meta.requiresAuth && to.meta.roles && user && !to.meta.roles.includes(user.role)) {
        next({ name: dashboardRouteByRole[user.role] || 'login' })
        return
    }
    if (guestOnlyPages.includes(to.name) && token) {
        next({ name: dashboardRouteByRole[user?.role] || 'dashboard.backer' })
        return
    }
    next()
})

export default router

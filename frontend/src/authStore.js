import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/services/authService'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(JSON.parse(localStorage.getItem('user')) || null)
    const token = ref(localStorage.getItem('token') || null)
    const isLoading = ref(false)

    const isAuthenticated = computed(() => !!token.value)

    function setSession(userData, authToken) {
        user.value = userData
        token.value = authToken
        localStorage.setItem('user', JSON.stringify(userData))
        localStorage.setItem('token', authToken)
    }

    function clearSession() {
        user.value = null
        token.value = null
        localStorage.removeItem('user')
        localStorage.removeItem('token')
    }

    async function login(email, password) {
        isLoading.value = true
        try {
            const res = await authService.login(email, password)
            setSession(res.data.user, res.data.token)
            return res
        } finally {
            isLoading.value = false
        }
    }

    async function register(name, email, password, password_confirmation) {
        isLoading.value = true
        try {
            const res = await authService.register(name, email, password, password_confirmation)
            return res
        } finally {
            isLoading.value = false
        }
    }

    async function logout() {
        try {
            await authService.logout()
        } finally {
            clearSession()
        }
    }

    async function fetchProfile() {
        const res = await authService.getProfile()
        user.value = res.data
        localStorage.setItem('user', JSON.stringify(res.data))
        return res
    }

    return {
        user, token, isLoading, isAuthenticated,
        login, register, logout, fetchProfile, setSession, clearSession,
    }
})
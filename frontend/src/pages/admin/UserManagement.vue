<script setup>
import { onMounted, ref, reactive, watch } from 'vue'
import { useToast } from 'vue-toastification'
import dayjs from 'dayjs'
import { adminService } from '@/services/adminService'

const toast = useToast()
const users = ref([])
const isLoading = ref(false)

const filters = reactive({ role: '', search: '' })

async function loadUsers() {
    isLoading.value = true
    try {
        const res = await adminService.getUsers({
            role: filters.role || undefined,
            search: filters.search || undefined,
        })
        users.value = res.data.data
    } finally {
        isLoading.value = false
    }
}

onMounted(loadUsers)
watch(filters, loadUsers)

async function toggleSuspend(user) {
    try {
        if (user.suspended_at) {
            await adminService.activateUser(user.id)
            toast.success(`${user.name} diaktifkan kembali`)
        } else {
            await adminService.suspendUser(user.id)
            toast.success(`${user.name} disuspend`)
        }
        loadUsers()
    } catch (error) {
        toast.error(error.response?.data?.message || 'Gagal memperbarui status user')
    }
}

const roleLabel = { guest: 'Guest', backer: 'Backer', creator: 'Creator', admin: 'Admin' }
</script>

<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Manajemen User</h1>

        <div class="flex flex-wrap gap-3 mb-4">
            <input v-model="filters.search" placeholder="Cari nama/email..." class="border rounded-sm px-3 py-2 text-sm w-64" />
            <select v-model="filters.role" class="border rounded-sm px-3 py-2 text-sm">
                <option value="">Semua Role</option>
                <option value="backer">Backer</option>
                <option value="creator">Creator</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div v-if="isLoading" class="text-center py-20 text-gray-500">Memuat user...</div>
        <div v-else-if="users.length === 0" class="text-center py-20 text-gray-500">Tidak ada user ditemukan.</div>

        <table v-else class="w-full text-sm border rounded-xl overflow-hidden">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Bergabung</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="u in users" :key="u.id" class="border-t">
                    <td class="p-3 font-medium">{{ u.name }}</td>
                    <td class="p-3 text-gray-500">{{ u.email }}</td>
                    <td class="p-3">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ roleLabel[u.role] }}</span>
                    </td>
                    <td class="p-3 text-gray-500">{{ dayjs(u.created_at).format('DD MMM YYYY') }}</td>
                    <td class="p-3">
                        <span v-if="u.suspended_at" class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700">Suspended</span>
                        <span v-else class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
                    </td>
                    <td class="p-3">
                        <button v-if="u.role !== 'admin'" @click="toggleSuspend(u)"
                            class="text-xs font-semibold px-3 py-1.5 rounded-sm"
                            :class="u.suspended_at ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
                            {{ u.suspended_at ? 'Aktifkan' : 'Suspend' }}
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
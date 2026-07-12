<script setup>
import { onMounted, ref, reactive, computed } from 'vue'
import dayjs from 'dayjs'
import { adminService } from '@/services/adminService'

const allUsers = ref([])
const isLoading = ref(false)
const filters = reactive({ role: '', search: '' })

async function loadUsers() {
    isLoading.value = true
    try {
        const res = await adminService.getUsers()
        allUsers.value = res.data.data
    } finally {
        isLoading.value = false
    }
}

onMounted(loadUsers)

const users = computed(() => allUsers.value.filter(u => {
    const matchRole = !filters.role || u.role === filters.role
    const matchSearch = !filters.search ||
        u.name.toLowerCase().includes(filters.search.toLowerCase()) ||
        u.email.toLowerCase().includes(filters.search.toLowerCase())
    return matchRole && matchSearch
}))

const roleLabel = { backer: 'Backer', creator: 'Creator', admin: 'Admin' }
</script>

<template>
    <div class="p-4 sm:p-8">
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
                    <th class="p-3">Kampanye</th>
                    <th class="p-3">Backing</th>
                    <th class="p-3">Bergabung</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="u in users" :key="u.id" class="border-t">
                    <td class="p-3 font-medium">{{ u.name }}</td>
                    <td class="p-3 text-gray-500">{{ u.email }}</td>
                    <td class="p-3"><span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ roleLabel[u.role] }}</span></td>
                    <td class="p-3 text-gray-500">{{ u.campaign_count }}</td>
                    <td class="p-3 text-gray-500">{{ u.backing_count }}</td>
                    <td class="p-3 text-gray-500">{{ dayjs(u.created_at).format('DD MMM YYYY') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
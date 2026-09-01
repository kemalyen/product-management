<template>
    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Users
                    </h3>
                    <button
                        v-if="auth.hasRole('Admin')"
                        @click="openModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Create User
                    </button>
                </div>

                <div v-if="loading" class="text-gray-500 py-4">
                    Loading...
                </div>
                <div v-else-if="error" class="text-red-600 py-4">
                    {{ error }}
                </div>
                <div v-else-if="users.length === 0" class="text-gray-500 py-4">
                    No users found.
                </div>

                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" @click="sort('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none">
                                <span class="inline-flex items-center">Name <SortIcon :active="sortBy === 'name'" :direction="sortDirection" /></span>
                            </th>
                            <th scope="col" @click="sort('email')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none">
                                <span class="inline-flex items-center">Email <SortIcon :active="sortBy === 'email'" :direction="sortDirection" /></span>
                            </th>
                            <th scope="col"  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none">
                                <span class="inline-flex items-center">Role </span>
                            </th>
                            <th v-if="auth.hasRole('Admin')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ user.id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ user.attributes?.name || user.name || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user.attributes?.email || user.email || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user?.relationships?.roles?.data?.name || '—' }}
                            </td>
                            <td v-if="auth.hasRole('Admin')" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openModal(user)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button @click="confirmDelete(user)" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="pagination && pagination.last_page > 1" class="mt-4 flex items-center justify-between">
                    <button
                        @click="goToPage(pagination.current_page - 1)"
                        :disabled="pagination.current_page <= 1"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Previous
                    </button>
                    <div class="flex space-x-1">
                        <button
                            v-for="link in pageLinks"
                            :key="link.page"
                            @click="goToPage(link.page)"
                            :disabled="link.active"
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                            :class="link.active
                                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed'"
                        >
                            {{ link.label }}
                        </button>
                    </div>
                    <button
                        @click="goToPage(pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        {{ isEditing ? 'Edit User' : 'Create User' }}
                    </h3>
                    <form @submit.prevent="handleSubmit">
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-base font-medium text-gray-700">Name</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base border-2 px-4 py-2"
                                />
                            </div>
                            <div>
                                <label for="email" class="block text-base font-medium text-gray-700">Email</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-2"
                                />
                            </div>
                            <div>
                                <label for="role" class="block text-base font-medium text-gray-700">Role</label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-2"
                                >
                                    <option value="">Select a role</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Account User">Account User</option>
                                    <option value="Account Api User">Account Api User</option>
                                </select>
                            </div>
                            <div v-if="!isEditing">
                                <label for="password" class="block text-base font-medium text-gray-700">Password</label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-2"
                                />
                            </div>
                            <div v-if="!isEditing && auth.hasRole('Admin')">
                                <label for="account_id" class="block text-base font-medium text-gray-700">Account</label>
                                <select
                                    id="account_id"
                                    v-model="form.account_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-2"
                                >
                                    <option value="">Select an account</option>
                                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                                        {{ account.attributes?.name || 'Account ' + account.id }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-flow-row-dense">
                            <button
                                type="submit"
                                :disabled="formLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-3 bg-indigo-600 text-lg font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 col-start-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ formLoading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
                            </button>
                            <button
                                type="button"
                                @click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-3 bg-white text-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 col-start-1 sm:mt-0"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                        Delete User
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to delete {{ selectedUser?.attributes?.name || selectedUser?.name }}? This action cannot be undone.
                    </p>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="handleDelete"
                            :disabled="deleteLoading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ deleteLoading ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button
                            type="button"
                            @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useUsers } from '../composables/useUsers'
import { useAccounts } from '../composables/useAccounts'
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'
import SortIcon from '../components/SortIcon.vue'

const auth = useAuth()
const toast = useToast()
const {
    users,
    pagination,
    loading,
    error,
    fetchUsers,
    goToPage,
    createUser,
    updateUser,
    deleteUser,
} = useUsers()

const {
    accounts,
    fetchAccounts,
} = useAccounts()

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const formLoading = ref(false)
const deleteLoading = ref(false)
const selectedUser = ref(null)
const sortBy = ref('')
const sortDirection = ref('asc')

const form = reactive({
    name: '',
    email: '',
    password: '',
    role: '',
    account_id: '',
})

onMounted(() => {
    fetchUsers(1)
})

const sort = (column) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortDirection.value = 'desc'
    }
    fetchUsers(1, sortBy.value, sortDirection.value)
}

const pageLinks = computed(() => {
    if (!pagination.links || pagination.links.length === 0) {
        return []
    }

    return pagination.links
        .filter((link) => link.page !== null)
        .map((link) => ({
            page: link.page,
            label: link.label,
            active: link.active,
        }))
})

const openModal = async (user = null) => {
    if (user) {
        isEditing.value = true
        form.name = user.attributes?.name || user.name || ''
        form.email = user.attributes?.email || user.email || ''
        form.password = ''
        form.role = user?.relationships?.roles?.data?.name || ''
        form.account_id = ''
        selectedUser.value = user
    } else {
        isEditing.value = false
        form.name = ''
        form.email = ''
        form.password = ''
        form.role = ''
        form.account_id = ''
        selectedUser.value = null

        if (auth.hasRole('Admin')) {
            await fetchAccounts(1)
        }
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    isEditing.value = false
    selectedUser.value = null
    form.name = ''
    form.email = ''
    form.password = ''
    form.role = ''
    form.account_id = ''
}

const handleSubmit = async () => {
    formLoading.value = true

    try {
        const payload = {
            name: form.name,
            email: form.email,
            role: form.role,
        }

        if (!isEditing.value && form.password) {
            payload.password = form.password
        }

        if (isEditing.value && form.password) {
            payload.password = form.password
        }

        if (!isEditing.value && auth.hasRole('Admin') && form.account_id) {
            payload.account_id = form.account_id
        }

        let result
        if (isEditing.value && selectedUser.value) {
            result = await updateUser(selectedUser.value.id, payload)
            toast.success('User updated successfully.')
        } else {
            result = await createUser(payload)
            toast.success('User created successfully.')
        }

        closeModal()
        fetchUsers(1, sortBy.value, sortDirection.value)
    } catch (e) {
        error.value = e.message || 'Failed to save user'
    } finally {
        formLoading.value = false
    }
}

const confirmDelete = (user) => {
    selectedUser.value = user
    showDeleteModal.value = true
}

const handleDelete = async () => {
    if (!selectedUser.value) return

    deleteLoading.value = true

    try {
        await deleteUser(selectedUser.value.id)
        toast.success('User deleted successfully.')
        showDeleteModal.value = false
        selectedUser.value = null
        fetchUsers(1, sortBy.value, sortDirection.value)
    } catch (e) {
        error.value = e.message || 'Failed to delete user'
    } finally {
        deleteLoading.value = false
    }
}
</script>

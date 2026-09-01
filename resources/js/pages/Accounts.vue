<template>
    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Accounts
                    </h3>
                    <button
                        v-if="auth.hasRole('Admin')"
                        @click="openModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Create Account
                    </button>
                </div>

                <div v-if="loading" class="text-gray-500 py-4">
                    Loading...
                </div>
                <div v-else-if="error" class="text-red-600 py-4">
                    {{ error }}
                </div>
                <div v-else-if="accounts.length === 0" class="text-gray-500 py-4">
                    No accounts found.
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
                            <th scope="col" @click="sort('account_number')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none">
                                <span class="inline-flex items-center">Account Number <SortIcon :active="sortBy === 'account_number'" :direction="sortDirection" /></span>
                            </th>
                            <th scope="col" @click="sort('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none">
                                <span class="inline-flex items-center">Status <SortIcon :active="sortBy === 'status'" :direction="sortDirection" /></span>
                            </th>
                            <th v-if="auth.hasRole('Admin')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="account in accounts" :key="account.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ account.id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ account.attributes?.name || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ account.attributes?.account_number || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="statusClass(account.attributes?.status)">
                                    {{ account.attributes?.status?.toUpperCase() || '—' }}
                                </span>
                            </td>
                            <td v-if="auth.hasRole('Admin')" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openModal(account)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button @click="confirmDelete(account)" class="text-red-600 hover:text-red-900">Delete</button>
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
                        {{ isEditing ? 'Edit Account' : 'Create Account' }}
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-2"
                                />
                            </div>
                            <div>
                                <label for="account_number" class="block text-base font-medium text-gray-700">Account Number</label>
                                <input
                                    id="account_number"
                                    v-model="form.account_number"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-2"
                                />
                            </div>
                            <div>
                                <label for="status" class="block text-base font-medium text-gray-700">Status</label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-2"
                                >
                                    <option value="">Select</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="disabled">Disabled</option>
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
                        Delete Account
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to delete {{ selectedAccount?.attributes?.name || selectedAccount?.name }}? This action cannot be undone.
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
import { useAccounts } from '../composables/useAccounts'
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'
import SortIcon from '../components/SortIcon.vue'

const auth = useAuth()
const toast = useToast()
const {
    accounts,
    pagination,
    loading,
    error,
    fetchAccounts,
    goToPage,
    createAccount,
    updateAccount,
    deleteAccount,
} = useAccounts()

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const formLoading = ref(false)
const deleteLoading = ref(false)
const selectedAccount = ref(null)
const sortBy = ref('')
const sortDirection = ref('asc')

const form = reactive({
    name: '',
    account_number: '',
    status: '',
})

onMounted(() => {
    fetchAccounts(1)
})

const sort = (column) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortDirection.value = 'desc'
    }
    fetchAccounts(1, sortBy.value, sortDirection.value)
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

const openModal = (account = null) => {
    if (account) {
        isEditing.value = true
        form.name = account.attributes?.name || ''
        form.account_number = account.attributes?.account_number || ''
        form.status = account.attributes?.status || ''
        selectedAccount.value = account
    } else {
        isEditing.value = false
        form.name = ''
        form.account_number = ''
        form.status = ''
        selectedAccount.value = null
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    isEditing.value = false
    selectedAccount.value = null
    form.name = ''
    form.account_number = ''
    form.status = ''
}

const handleSubmit = async () => {
    formLoading.value = true

    try {
        const payload = {
            name: form.name,
            account_number: form.account_number,
            status: form.status,
        }

        if (isEditing.value && selectedAccount.value) {
            await updateAccount(selectedAccount.value.id, payload)
            toast.success('Account updated successfully.')
        } else {
            await createAccount(payload)
            toast.success('Account created successfully.')
        }

        closeModal()
        fetchAccounts(1, sortBy.value, sortDirection.value)
    } catch (e) {
        error.value = e.message || 'Failed to save account'
    } finally {
        formLoading.value = false
    }
}

const confirmDelete = (account) => {
    selectedAccount.value = account
    showDeleteModal.value = true
}

const handleDelete = async () => {
    if (!selectedAccount.value) return

    deleteLoading.value = true

    try {
        await deleteAccount(selectedAccount.value.id)
        toast.success('Account deleted successfully.')
        showDeleteModal.value = false
        selectedAccount.value = null
        fetchAccounts(1, sortBy.value, sortDirection.value)
    } catch (e) {
        error.value = e.message || 'Failed to delete account'
    } finally {
        deleteLoading.value = false
    }
}

const statusClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800'
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'disabled':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}
</script>

<template>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        User Profile
                    </h3>
                    <button
                        v-if="!editing"
                        @click="enterEdit"
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Edit
                    </button>
                </div>

                <div v-if="error" class="mt-4 p-3 rounded-md bg-red-50 border border-red-200">
                    <p class="text-sm text-red-800">{{ error }}</p>
                </div>

                <div class="mt-6 border-t border-gray-200">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd v-if="!editing" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ user?.data?.attributes?.name || '—' }}
                            </dd>
                            <dd v-else class="mt-1 sm:mt-0 sm:col-span-2">
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border"
                                />
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd v-if="!editing" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ user?.data?.attributes?.email || '—' }}
                            </dd>
                            <dd v-else class="mt-1 sm:mt-0 sm:col-span-2">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border"
                                />
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ roleName || '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div v-if="editing" class="mt-4 flex space-x-3 justify-end">
                    <button
                        type="button"
                        @click="cancelEdit"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="save"
                        :disabled="saving"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ saving ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="account" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Account Details
                </h3>
                <div class="mt-6 border-t border-gray-200">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Account Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ account.name || '—' }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Account Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ account.account_number || '—' }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="statusClass(account.status)">
                                    {{ account.status || '—' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'

const auth = useAuth()
const toast = useToast()

const user = computed(() => auth.user.value)
const roleName = computed(() => auth.role.value?.name)
const account = computed(() => auth.account.value)

const editing = ref(false)
const saving = ref(false)
const error = ref('')
const form = reactive({
    name: '',
    email: '',
})

const enterEdit = () => {
    form.name = user.value?.data?.attributes?.name || ''
    form.email = user.value?.data?.attributes?.email || ''
    error.value = ''
    editing.value = true
}

const cancelEdit = () => {
    editing.value = false
    error.value = ''
}

const save = async () => {
    saving.value = true
    error.value = ''

    try {
        await auth.updateProfile({
            name: form.name,
            email: form.email,
        })
        toast.success('Profile updated successfully.')
        editing.value = false
    } catch (e) {
        error.value = e.message || 'Failed to update profile'
    } finally {
        saving.value = false
    }
}

const statusClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800'
        case 'disabled':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}
</script>
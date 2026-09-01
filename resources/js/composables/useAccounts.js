import { ref, reactive } from 'vue'

export function useAccounts() {
    const accounts = ref([])
    const pagination = reactive({
        current_page: 1,
        last_page: 1,
        total: 0,
        links: [],
    })
    const loading = ref(false)
    const error = ref('')

    const fetchAccounts = async (page = 1, sortBy = null, sortDirection = 'asc') => {
        loading.value = true
        error.value = ''

        try {
            const params = new URLSearchParams({ page })
            if (sortBy) {
                const sortParam = sortDirection === 'desc' ? `-${sortBy}` : sortBy
                params.append('sort', sortParam)
            }

            const response = await fetch(`/api/accounts?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                },
            })

            if (!response.ok) {
                throw new Error('Failed to fetch accounts')
            }

            const data = await response.json()
            accounts.value = data.data
            pagination.current_page = data.meta.current_page
            pagination.last_page = data.meta.last_page
            pagination.total = data.meta.total
            pagination.links = data.meta.links
        } catch (e) {
            error.value = e.message || 'Failed to load accounts'
        } finally {
            loading.value = false
        }
    }

    const goToPage = (page) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchAccounts(page)
        }
    }

    const showAccount = async (id) => {
        const response = await fetch(`/api/accounts/${id}`, {
            headers: {
                'Accept': 'application/json',
            },
        })

        if (!response.ok) {
            throw new Error('Failed to fetch account')
        }

        return response.json()
    }

    const createAccount = async (payload) => {
        const response = await fetch('/api/accounts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })

        if (!response.ok) {
            const error = await response.json()
            throw new Error(error.message || 'Failed to create account')
        }

        return response.json()
    }

    const updateAccount = async (id, payload) => {
        const response = await fetch(`/api/accounts/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })

        if (!response.ok) {
            const error = await response.json()
            throw new Error(error.message || 'Failed to update account')
        }

        return response.json()
    }

    const deleteAccount = async (id) => {
        const response = await fetch(`/api/accounts/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
            },
        })

        if (!response.ok) {
            throw new Error('Failed to delete account')
        }
    }

    return {
        accounts,
        pagination,
        loading,
        error,
        fetchAccounts,
        goToPage,
        showAccount,
        createAccount,
        updateAccount,
        deleteAccount,
    }
}

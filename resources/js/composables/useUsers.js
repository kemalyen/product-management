import { ref, reactive } from 'vue'

export function useUsers() {
    const users = ref([])
    const pagination = reactive({
        current_page: 1,
        last_page: 1,
        total: 0,
        links: [],
    })
    const loading = ref(false)
    const error = ref('')

    const fetchUsers = async (page = 1, sortBy = null, sortDirection = 'asc') => {
        loading.value = true
        error.value = ''

        try {
            const params = new URLSearchParams({ page })
            if (sortBy) {
                const sortParam = sortDirection === 'desc' ? `-${sortBy}` : sortBy
                params.append('sort', sortParam)
            }

            const response = await fetch(`/api/users?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                },
            })

            if (!response.ok) {
                throw new Error('Failed to fetch users')
            }

            const data = await response.json()
            users.value = data.data
            pagination.current_page = data.meta.current_page
            pagination.last_page = data.meta.last_page
            pagination.total = data.meta.total
            pagination.links = data.meta.links
        } catch (e) {
            error.value = e.message || 'Failed to load users'
        } finally {
            loading.value = false
        }
    }

    const goToPage = (page) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchUsers(page)
        }
    }

    const showUser = async (id) => {
        const response = await fetch(`/api/users/${id}`, {
            headers: {
                'Accept': 'application/json',
            },
        })

        if (!response.ok) {
            throw new Error('Failed to fetch user')
        }

        return response.json()
    }

    const createUser = async (payload) => {
        const response = await fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })

        if (!response.ok) {
            const error = await response.json()
            throw new Error(error.message || 'Failed to create user')
        }

        return response.json()
    }

    const updateUser = async (id, payload) => {
        const response = await fetch(`/api/users/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })

        if (!response.ok) {
            const error = await response.json()
            throw new Error(error.message || 'Failed to update user')
        }

        return response.json()
    }

    const deleteUser = async (id) => {
        const response = await fetch(`/api/users/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
            },
        })

        if (!response.ok) {
            throw new Error('Failed to delete user')
        }
    }

    return {
        users,
        pagination,
        loading,
        error,
        fetchUsers,
        goToPage,
        showUser,
        createUser,
        updateUser,
        deleteUser,
    }
}

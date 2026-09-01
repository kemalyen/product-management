import { ref, computed } from 'vue'

const user = ref(null)

export function useAuth() {
    const isAuthenticated = computed(() => !!user.value)

    const role = computed(() => {
        if (!user.value?.data?.relationships?.roles?.data) {
            return null
        }
        return user.value.data.relationships.roles.data
    })

    const account = computed(() => {
        if (!user.value?.data?.includes?.attributes) {
            return null
        }
        return user.value.data.includes.attributes
    })

    const hasRole = (roleName) => {
        if (!role.value) return false
        return role.value.name === roleName
    }

    const login = async (email, password) => {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email, password }),
        })

        if (!response.ok) {
            const error = await response.json()
            throw new Error(error.message || 'Login failed')
        }

        const data = await response.json()
        user.value = data.user

        return data
    }

    const logout = async () => {
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
            })
        } finally {
            user.value = null
        }
    }

    const fetchUser = async () => {
        const response = await fetch('/api/me', {
            headers: {
                'Accept': 'application/json',
            },
        })

        if (response.ok) {
            const data = await response.json()
            user.value = data.user
            return data
        }

        user.value = null
        return null
    }

    const updateProfile = async (payload) => {
        const id = user.value?.data?.id
        if (!id) {
            throw new Error('User not loaded')
        }

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
            throw new Error(error.message || 'Failed to update profile')
        }

        const data = await response.json()
        user.value = data
        return data
    }

    return {
        user,
        isAuthenticated,
        role,
        account,
        hasRole,
        login,
        logout,
        fetchUser,
        updateProfile,
    }
}

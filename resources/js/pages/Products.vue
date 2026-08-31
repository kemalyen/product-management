<template>
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Filters
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            id="name"
                            v-model="filters.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Search name..."
                        />
                    </div>
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                        <input
                            id="sku"
                            v-model="filters.sku"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Search SKU..."
                        />
                    </div>
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
                        <input
                            id="barcode"
                            v-model="filters.barcode"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Search barcode..."
                        />
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            v-model="filters.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="disabled">Disabled</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label for="stock_operator" class="block text-sm font-medium text-gray-700">Stock</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <select
                                id="stock_operator"
                                v-model="filters.stock_operator"
                                class="rounded-l-md border-r-0 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">--</option>
                                <option value=">">&gt;</option>
                                <option value="<">&lt;</option>
                                <option value=">=">&gt;=</option>
                                <option value="<=">&lt;=</option>
                                <option value="=">=</option>
                            </select>
                            <input
                                v-model="filters.stock_value"
                                type="number"
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Value"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="price_operator" class="block text-sm font-medium text-gray-700">Price</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <select
                                id="price_operator"
                                v-model="filters.price_operator"
                                class="rounded-l-md border-r-0 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">--</option>
                                <option value=">">&gt;</option>
                                <option value="<">&lt;</option>
                                <option value=">=">&gt;=</option>
                                <option value="<=">&lt;=</option>
                                <option value="=">=</option>
                            </select>
                            <input
                                v-model="filters.price_value"
                                type="number"
                                step="0.01"
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Value"
                            />
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex space-x-3">
                    <button
                        @click="applyFilters"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Apply Filters
                    </button>
                    <button
                        @click="resetFilters"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Products
                    </h3>
                    <button
                        v-if="auth.hasRole('Admin')"
                        @click="openModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Create Product
                    </button>
                </div>

                <div v-if="loading" class="text-gray-500 py-4">
                    Loading...
                </div>
                <div v-else-if="error" class="text-red-600 py-4">
                    {{ error }}
                </div>
                <div v-else-if="products.length === 0" class="text-gray-500 py-4">
                    No products found.
                </div>

                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                SKU
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Barcode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th v-if="auth.hasRole('Admin')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in products" :key="product.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ product.id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ product.attributes.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ product.attributes.sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ product.attributes.barcode }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="statusClass(product.attributes.status)">
                                    {{ product.attributes.status.toUpperCase() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ product.attributes.stock }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${{ product.attributes.price }}
                            </td>
                            <td v-if="auth.hasRole('Admin')" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openModal(product)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button @click="confirmDelete(product)" class="text-red-600 hover:text-red-900">Delete</button>
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
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        {{ isEditing ? 'Edit Product' : 'Create Product' }}
                    </h3>
                    <form @submit.prevent="handleSubmit">
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                ></textarea>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                    <input
                                        id="sku"
                                        v-model="form.sku"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                                <div>
                                    <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
                                    <input
                                        id="barcode"
                                        v-model="form.barcode"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                        <option value="">Select</option>
                                        <option value="active">Active</option>
                                        <option value="pending">Pending</option>
                                        <option value="disabled">Disabled</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                                    <input
                                        id="stock"
                                        v-model="form.stock"
                                        type="number"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                    <input
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button
                                type="submit"
                                :disabled="formLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ formLoading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
                            </button>
                            <button
                                type="button"
                                @click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
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
                        Delete Product
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to delete {{ selectedProduct?.attributes?.name || selectedProduct?.name }}? This action cannot be undone.
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
import { useProducts } from '../composables/useProducts'
import { useAuth } from '../composables/useAuth'

const auth = useAuth()
const {
    products,
    pagination,
    loading,
    error,
    filters,
    fetchProducts,
    applyFilters,
    resetFilters,
    goToPage,
    createProduct,
    updateProduct,
    deleteProduct,
} = useProducts()

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const formLoading = ref(false)
const deleteLoading = ref(false)
const selectedProduct = ref(null)

const form = reactive({
    name: '',
    description: '',
    sku: '',
    barcode: '',
    status: '',
    stock: '',
    price: '',
})

onMounted(() => {
    fetchProducts(1)
})

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

const openModal = (product = null) => {
    if (product) {
        isEditing.value = true
        form.name = product.attributes?.name || ''
        form.description = product.attributes?.description || ''
        form.sku = product.attributes?.sku || ''
        form.barcode = product.attributes?.barcode || ''
        form.status = product.attributes?.status || ''
        form.stock = product.attributes?.stock ?? ''
        form.price = product.attributes?.price ?? ''
        selectedProduct.value = product
    } else {
        isEditing.value = false
        form.name = ''
        form.description = ''
        form.sku = ''
        form.barcode = ''
        form.status = ''
        form.stock = ''
        form.price = ''
        selectedProduct.value = null
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    isEditing.value = false
    selectedProduct.value = null
    form.name = ''
    form.description = ''
    form.sku = ''
    form.barcode = ''
    form.status = ''
    form.stock = ''
    form.price = ''
}

const handleSubmit = async () => {
    formLoading.value = true

    try {
        const payload = {
            name: form.name,
            description: form.description,
            sku: form.sku,
            barcode: form.barcode,
            status: form.status,
            stock: form.stock,
            price: form.price,
        }

        if (isEditing.value && selectedProduct.value) {
            await updateProduct(selectedProduct.value.id, payload)
        } else {
            await createProduct(payload)
        }

        closeModal()
        fetchProducts(pagination.current_page)
    } catch (e) {
        error.value = e.message || 'Failed to save product'
    } finally {
        formLoading.value = false
    }
}

const confirmDelete = (product) => {
    selectedProduct.value = product
    showDeleteModal.value = true
}

const handleDelete = async () => {
    if (!selectedProduct.value) return

    deleteLoading.value = true

    try {
        await deleteProduct(selectedProduct.value.id)
        showDeleteModal.value = false
        selectedProduct.value = null
        fetchProducts(pagination.current_page)
    } catch (e) {
        error.value = e.message || 'Failed to delete product'
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
        case 'inactive':
            return 'bg-gray-100 text-gray-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (date) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString()
}
</script>

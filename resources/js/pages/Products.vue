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
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Products
                </h3>

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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Published
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(product.attributes.publishedAt) }}
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
    </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useProducts } from '../composables/useProducts'

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
} = useProducts()

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

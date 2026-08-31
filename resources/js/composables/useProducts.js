import { ref, reactive } from 'vue'

export function useProducts() {
    const products = ref([])
    const pagination = reactive({
        current_page: 1,
        last_page: 1,
        total: 0,
        links: [],
    })
    const loading = ref(false)
    const error = ref('')

    const filters = reactive({
        name: '',
        sku: '',
        barcode: '',
        status: '',
        stock_operator: '',
        stock_value: '',
        price_operator: '',
        price_value: '',
    })

    const buildQuery = () => {
        const params = new URLSearchParams()

        if (filters.name) {
            params.append('filter[name]', `*${filters.name}*`)
        }
        if (filters.sku) {
            params.append('filter[sku]', filters.sku)
        }
        if (filters.barcode) {
            params.append('filter[barcode]', filters.barcode)
        }
        if (filters.status) {
            params.append('filter[status]', filters.status)
        }

        if (filters.stock_operator && filters.stock_value) {
            params.append(`filter[stock]${filters.stock_operator}`, filters.stock_value)
        }

        if (filters.price_operator && filters.price_value) {
            params.append(`filter[price]${filters.price_operator}`, filters.price_value)
        }

        return params.toString()
    }

    const fetchProducts = async (page = 1) => {
        loading.value = true
        error.value = ''

        try {
            const query = buildQuery()
            const url = `/api/products?page=${page}${query ? '&' + query : ''}`

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                },
            })

            if (!response.ok) {
                throw new Error('Failed to fetch products')
            }

            const data = await response.json()
            products.value = data.data
            pagination.current_page = data.meta.current_page
            pagination.last_page = data.meta.last_page
            pagination.total = data.meta.total
            pagination.links = data.meta.links
        } catch (e) {
            error.value = e.message || 'Failed to load products'
        } finally {
            loading.value = false
        }
    }

    const applyFilters = () => {
        fetchProducts(1)
    }

    const resetFilters = () => {
        filters.name = ''
        filters.sku = ''
        filters.barcode = ''
        filters.status = ''
        filters.stock_operator = ''
        filters.stock_value = ''
        filters.price_operator = ''
        filters.price_value = ''
        fetchProducts(1)
    }

    const goToPage = (page) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchProducts(page)
        }
    }

    return {
        products,
        pagination,
        loading,
        error,
        filters,
        fetchProducts,
        applyFilters,
        resetFilters,
        goToPage,
    }
}

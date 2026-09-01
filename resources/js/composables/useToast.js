import { ref } from 'vue'

const toasts = ref([])
let nextId = 1

const push_ = (message, type) => {
    const id = nextId++
    toasts.value.push({ id, message, type })
    setTimeout(() => {
        toasts.value = toasts.value.filter((t) => t.id !== id)
    }, 4000)
}

const success = (message) => push_(message, 'success')
const error = (message) => push_(message, 'error')
const info = (message) => push_(message, 'info')

export function useToast() {
    return {
        toasts,
        success,
        error,
        info,
    }
}
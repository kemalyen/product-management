<template>
    <div class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none">
        <transition-group name="toast" tag="div" class="space-y-2">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto min-w-[280px] max-w-sm rounded-md shadow-lg px-4 py-3 text-sm font-medium text-white"
                :class="classFor(toast.type)"
                role="alert"
            >
                {{ toast.message }}
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { useToast } from '../composables/useToast'

const { toasts } = useToast()

const classFor = (type) => {
    switch (type) {
        case 'success':
            return 'bg-green-600'
        case 'error':
            return 'bg-red-600'
        case 'info':
        default:
            return 'bg-blue-600'
    }
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(20px);
}
.toast-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
</style>
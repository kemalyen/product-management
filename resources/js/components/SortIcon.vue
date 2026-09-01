<template>
    <span class="inline-flex flex-col items-center justify-center ml-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" :class="active && direction === 'asc' ? 'text-indigo-600' : 'text-gray-400'">
            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 -mt-1" viewBox="0 0 20 20" fill="currentColor" :class="active && direction === 'desc' ? 'text-indigo-600' : 'text-gray-400'">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 11.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </span>
</template>

<script setup>
defineProps({
    active: Boolean,
    direction: {
        type: String,
        default: 'asc',
    },
})
</script>

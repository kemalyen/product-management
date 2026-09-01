import { onMounted } from 'vue'

export function usePageTitle(title) {
    onMounted(() => {
        document.title = title
    })
}

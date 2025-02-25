import { ref } from 'vue'

// Create a dedicated state object to avoid global naming conflicts
const sidebarState = {
  isOpen: ref(false)
}

export function useToggleSidebar() {
  const toggle = () => {
    sidebarState.isOpen.value = !sidebarState.isOpen.value
  }
  
  const hide = () => {
    sidebarState.isOpen.value = false
  }
  
  const show = () => {
    sidebarState.isOpen.value = true
  }
  
  return {
    isOpen: sidebarState.isOpen,
    toggle,
    hide,
    show
  }
}
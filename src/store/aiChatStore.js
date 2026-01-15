import { ref, readonly } from 'vue'

// Estado global del chat de IA
const isAIChatOpen = ref(false)

// Acciones
const openAIChat = () => {
  isAIChatOpen.value = true
}

const closeAIChat = () => {
  isAIChatOpen.value = false
}

const toggleAIChat = () => {
  isAIChatOpen.value = !isAIChatOpen.value
}

// Exportar como store
export const aiChatStore = {
  isOpen: readonly(isAIChatOpen),
  open: openAIChat,
  close: closeAIChat,
  toggle: toggleAIChat
}

// Export individual para uso directo
export { isAIChatOpen, openAIChat, closeAIChat, toggleAIChat }

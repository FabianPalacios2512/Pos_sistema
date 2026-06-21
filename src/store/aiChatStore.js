import { ref, readonly } from 'vue'

// Estado global del chat de IA
const isAIChatOpen = ref(false)
const chatMode = ref('sidebar') // 'sidebar' | 'centered'

// Acciones
const openAIChat = (mode = 'sidebar') => {
  chatMode.value = mode
  isAIChatOpen.value = true
}

const closeAIChat = () => {
  isAIChatOpen.value = false
  setTimeout(() => { chatMode.value = 'sidebar' }, 300)
}

const toggleAIChat = (mode = 'sidebar') => {
  if (isAIChatOpen.value && chatMode.value === mode) {
    isAIChatOpen.value = false
  } else {
    chatMode.value = mode
    isAIChatOpen.value = true
  }
}

// Exportar como store
export const aiChatStore = {
  isOpen: readonly(isAIChatOpen),
  mode: readonly(chatMode),
  open: openAIChat,
  close: closeAIChat,
  toggle: toggleAIChat
}

// Export individual para uso directo
export { isAIChatOpen, chatMode, openAIChat, closeAIChat, toggleAIChat }

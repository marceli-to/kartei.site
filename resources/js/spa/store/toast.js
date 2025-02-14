import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([]);

  const show = (message, type = 'info', duration = 3000, position = 'top-right') => {
    const id = Date.now();
    toasts.value.push({ id, message, type, position });

    if (duration > 0) {
      setTimeout(() => removeToast(id), duration);
    }
  };

  const removeToast = (id) => {
    toasts.value = toasts.value.filter(toast => toast.id !== id);
  };

  return { toasts, show, removeToast };
});
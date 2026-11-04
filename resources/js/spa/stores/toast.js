import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([]);
  // Counter instead of Date.now(): several toasts in the same ms need unique ids
  let nextId = 1;

  const show = (message, type = 'info', duration = 3000) => {
    const id = nextId++;
    toasts.value.push({ id, message, type });

    if (duration > 0) {
      setTimeout(() => removeToast(id), duration);
    }
  };

  const removeToast = (id) => {
    toasts.value = toasts.value.filter((toast) => toast.id !== id);
  };

  return { toasts, show, removeToast };
});

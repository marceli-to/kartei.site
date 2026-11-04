import { defineStore } from 'pinia';
import { ref, markRaw } from 'vue';

// Labels stay empty so Dialog.vue can fall back to its German defaults
const defaults = () => ({
  title: '',
  message: '',
  confirmLabel: '',
  cancelLabel: '',
  onConfirm: null,
  onCancel: null,
  component: null,
  props: null,
  size: 'medium',
});

export const useDialogStore = defineStore('dialog', () => {
  const isVisible = ref(false);
  const isBusy = ref(false);
  const content = ref(defaults());

  // Bumped on every show(), so an awaited confirm can tell whether it may still close "its" dialog
  const version = ref(0);

  // Pending reset from hide(); must not wipe a dialog opened right after
  let resetTimer = null;

  const show = (dialogContent) => {
    clearTimeout(resetTimer);
    resetTimer = null;
    version.value++;
    isBusy.value = false;
    content.value = {
      ...defaults(),
      ...dialogContent,
      component: dialogContent.component ? markRaw(dialogContent.component) : null
    };
    isVisible.value = true;
  };

  const hide = () => {
    isVisible.value = false;
    isBusy.value = false;
    clearTimeout(resetTimer);
    resetTimer = setTimeout(() => {
      resetTimer = null;
      content.value = defaults();
    }, 300);
  };

  return {
    isVisible,
    isBusy,
    content,
    version,
    show,
    hide
  };
});

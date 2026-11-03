import { defineStore } from 'pinia';
import { ref, markRaw } from 'vue';

export const useDialogStore = defineStore('dialog', () => {
  const isVisible = ref(false);
  const content = ref({
    title: '',
    confirmLabel: 'Confirm',
    cancelLabel: 'Cancel',
    onConfirm: null,
    onCancel: null,
    component: null,
    props: null,
    size: 'medium' // Default size
  });

  // Pending reset from hide(); must not wipe a dialog opened right after
  let resetTimer = null;

  const show = (dialogContent) => {
    clearTimeout(resetTimer);
    resetTimer = null;
    content.value = {
      ...content.value,
      ...dialogContent,
      component: dialogContent.component ? markRaw(dialogContent.component) : null
    };
    isVisible.value = true;
  };

  const hide = () => {
    isVisible.value = false;
    clearTimeout(resetTimer);
    resetTimer = setTimeout(() => {
      resetTimer = null;
      content.value = {
        title: '',
        confirmLabel: 'Confirm',
        cancelLabel: 'Cancel',
        onConfirm: null,
        onCancel: null,
        component: null,
        props: null,
        size: 'medium' // Reset to default size
      };
    }, 300);
  };

  return {
    isVisible,
    content,
    show,
    hide
  };
});
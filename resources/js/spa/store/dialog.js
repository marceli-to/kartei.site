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
    props: null
  });

  const show = (dialogContent) => {
    content.value = {
      ...content.value,
      ...dialogContent,
      component: dialogContent.component ? markRaw(dialogContent.component) : null
    };
    isVisible.value = true;
  };

  const hide = () => {
    isVisible.value = false;
    // Reset content after animation completes
    setTimeout(() => {
      content.value = {
        title: '',
        confirmLabel: 'Confirm',
        cancelLabel: 'Cancel',
        onConfirm: null,
        onCancel: null,
        component: null,
        props: null
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
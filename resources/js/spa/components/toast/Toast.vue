<template>
  <!-- Covers the header (same width, ends on its bottom line); fixed, so it stays in view
       when the page is scrolled. The layer lets clicks through; only the toasts catch them.
       Above the dialog (z-[9999]), so errors from a dialog action stay visible. -->
  <div
    :class="[
      'pointer-events-none fixed top-0 left-0 z-10000 w-full px-16 font-muoto-medium text-sm text-black',
      // While a message shows, the frame around it hides the scrolled content
      toasts.length > 0 ? 'bg-white pt-16 dark:bg-black' : '',
    ]"
    role="status"
    aria-live="polite">
    <button
      v-for="toast in toasts"
      :key="toast.id"
      type="button"
      :class="[
        'pointer-events-auto flex min-h-64 w-full items-center justify-center border-b border-black bg-graphite px-16 text-center dark:border-white',
        { 'bg-lime!': toast.type === 'success' },
        { 'bg-flame! text-white': toast.type === 'error' },
        { 'bg-ice!': toast.type === 'info' },
      ]"
      title="Meldung verbergen"
      @click="removeToast(toast.id)">
      {{ toast.message }}
    </button>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useToastStore } from '@/stores/toast';
const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);
const { removeToast } = toastStore;
</script>

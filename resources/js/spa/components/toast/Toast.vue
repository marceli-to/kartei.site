<template>
  <!-- The layer lets clicks through to the header; only the toasts themselves catch them.
       Above the dialog (z-[9999]), so errors from a dialog action stay visible. -->
  <div
    class="pointer-events-none fixed left-0 top-0 z-[10000] w-full px-16 font-muoto-medium text-sm text-black"
    role="status"
    aria-live="polite">
    <button
      v-for="toast in toasts"
      :key="toast.id"
      type="button"
      :class="[
        'pointer-events-auto mb-4 flex min-h-32 w-full items-center justify-center bg-graphite text-center',
        { '!bg-lime': toast.type === 'success' },
        { '!bg-flame text-white': toast.type === 'error' },
        { '!bg-ice': toast.type === 'info' },
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

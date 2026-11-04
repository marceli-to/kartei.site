<template>
  <!-- The layer lets clicks through to the header; only the toasts themselves catch them.
       Above the dialog (z-[9999]), so errors from a dialog action stay visible. -->
  <div 
    class="fixed z-[10000] text-sm w-full text-black font-muoto-medium top-0 left-0 px-16 pointer-events-none"
    role="status"
    aria-live="polite">
    <button 
      type="button"
      :class="[
        'pointer-events-auto w-full bg-graphite text-center min-h-32 mb-4 flex items-center justify-center',
        { '!bg-lime': toast.type === 'success' },
        { '!bg-flame text-white': toast.type === 'error' },
        { '!bg-ice': toast.type === 'info' },
      ]"
      @click="removeToast(toast.id)"
      title="Meldung verbergen"
      v-for="toast in toasts" :key="toast.id">
      {{ toast.message }}
    </button>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useToastStore } from '@/components/toast/stores/toast';
const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);
const { removeToast } = toastStore;
</script>

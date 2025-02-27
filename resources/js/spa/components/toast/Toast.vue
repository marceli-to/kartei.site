<template>
  <div 
    v-if="toasts.length" 
    class="fixed z-[9999] text-sm w-full text-black font-muoto-medium top-0 left-0 px-16">
    <a 
      href="javascript:;" 
      :class="[
        'bg-graphite text-center min-h-32 flex items-center justify-center max-page ml-auto',
        { '!bg-lime': toast.type === 'success' },
        { '!bg-flame text-white': toast.type === 'error' },
        { '!bg-ice': toast.type === 'info' },
      ]"
      @click="removeToast(toast.id)"
      title="Meldung verbergen"
      v-for="toast in toasts" :key="toast.id">
      {{ toast.message }}
    </a>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useToastStore } from '@/components/toast/stores/toast';
const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);
const { removeToast } = toastStore;
</script>

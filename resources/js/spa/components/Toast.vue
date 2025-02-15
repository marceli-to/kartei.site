<template>
  <div 
    v-if="toasts.length" 
    class="toasts">
    <a 
      href="javascript:;" 
      :class="['toast', toast.type]"
      @click="removeToast(toast.id)"
      title="Meldung verbergen"
      v-for="toast in toasts" :key="toast.id">
      {{ toast.message }}
    </a>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useToastStore } from '@/store/toast';

const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);
const { removeToast } = toastStore;

</script>

<style scoped>
.toasts {
  @apply fixed z-[9999] text-sm w-full text-black font-muoto-medium top-0 left-0;
}

.toast {
  @apply bg-graphite text-center min-h-32 flex items-center justify-center max-w-[1600px] mx-auto
}

.toast.success { 
  @apply bg-lime;
 }

.toast.error { 
  @apply bg-flame;
 }

.toast.info { 
  @apply bg-ice;
 }
</style>
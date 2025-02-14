<template>
  <div 
    v-if="toasts.length" 
    class="toasts" 
    :class="position">
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
import { computed } from 'vue';
import { useToastStore } from '@/store/toast';
import IconCross from '@/components/icons/Cross.vue';

const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);
const { removeToast } = toastStore;

const position = computed(() => toasts.value.length ? toasts.value[0].position : 'top-left');
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
/* .toasts.top-right {
  @apply top-10 right-10;
 }

.toasts.top-left {
  @apply top-0 left-0;
 }

.toasts.bottom-right {
  @apply bottom-10 right-10;
 }

.toasts.bottom-left {
  @apply bottom-10 left-10;
 } */


</style>
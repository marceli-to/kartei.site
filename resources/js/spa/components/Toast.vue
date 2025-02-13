<template>
  <div v-if="toasts.length" class="toasts" :class="position">
    <div v-for="toast in toasts" :key="toast.id" :class="['toast', toast.type]">
      {{ toast.message }}
      <button @click="removeToast(toast.id)">&times;</button>
    </div>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { computed } from 'vue';
import { useToastStore } from '@/store/toast';

const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);
const { removeToast } = toastStore;

const position = computed(() => toasts.value.length ? toasts.value[0].position : 'top-right');
</script>

<style scoped>
.toasts {
  @apply fixed z-[9999] text-sm;
}

.toasts.top-right {
  @apply top-10 right-10;
 }

.toasts.top-left {
  @apply top-10 left-10;
 }

.toasts.bottom-right {
  @apply bottom-10 right-10;
 }

.toasts.bottom-left {
  @apply bottom-10 left-10;
 }

.toast {
  @apply bg-graphite text-white py-4 px-8 mb-4 flex justify-between items-center
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
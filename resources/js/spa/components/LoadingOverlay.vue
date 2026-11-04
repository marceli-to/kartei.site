<template>
  <div
    v-if="isActive"
    class="fixed left-4 top-4 z-[9999] h-16 w-16 select-none">
    <svg
      width="24"
      height="24"
      viewBox="0 0 24 24"
      class="h-16 w-16 text-gray-800">
      <path
        d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
        class="spinner" />
    </svg>
  </div>
</template>

<script setup>
import { useGlobalLoading } from '@/composables/useGlobalLoading';
const { isActive } = useGlobalLoading();
</script>

<style scoped>
.spinner {
  transform-origin: center;
  animation: spin 0.75s linear infinite;
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

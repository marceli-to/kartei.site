<template>
  <nav 
    class="fixed top-16 right-0 z-80 bg-snow w-2/12 h-[calc(100dvh_-_1rem)] transform transition-transform duration-300 ease-in-out"
    :class="{ 'translate-x-0': isOpen, 'translate-x-full': !isOpen }"
  >
    <div class="p-8">
      <!-- Menu content goes here -->
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';

const isOpen = ref(false);
const toggle = () => {
  isOpen.value = !isOpen.value;
};

defineExpose({ toggle, isOpen });
</script>
<template>
  <nav
    class="fixed top-16 right-0 3xl:right-[calc((100%_-_100rem)/2)] z-80 bg-snow w-full max-w-[16rem] h-[calc(100dvh_-_1rem)] transform transition-transform duration-300 ease-in-out"
    :class="{ 'opacity-100': isOpen, 'opacity-0': !isOpen }"
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
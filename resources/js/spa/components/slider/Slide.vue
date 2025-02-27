<template>
  <div 
    class="w-full h-full relative px-8 pb-16"
    :class="{ '-top-24': pull }">
    <slot />
  </div>
</template>
<script setup>
defineProps({
  pull: {
    type: Boolean,
    default: false
  }
});
</script>
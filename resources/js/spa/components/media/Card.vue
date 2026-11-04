<template>
  <figure :class="['relative aspect-square border border-graphite bg-white dark:bg-black', classes]">
    <slot />
  </figure>
</template>
<script setup>
defineProps({
  classes: {
    type: String,
    default: '',
  },
});
</script>

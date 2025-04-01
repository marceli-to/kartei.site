<template>
  <figure :class="classes">
    <slot />
  </figure>
</template>
<script setup>
const props = defineProps({
  classes: {
    type: String,
    default: 'border border-graphite aspect-square relative bg-white dark:bg-black'
  }
})
</script>
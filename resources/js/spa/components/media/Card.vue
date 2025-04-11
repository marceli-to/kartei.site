<template>
  <figure 
    :class="[
      'border border-graphite aspect-square relative bg-white dark:bg-black',
      classes
    ]">
    <slot />
  </figure>
</template>
<script setup>
const props = defineProps({
  classes: {
    type: String,
    default: ''
  }
})
</script>
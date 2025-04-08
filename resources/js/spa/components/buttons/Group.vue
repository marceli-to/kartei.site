<template>
  <div :class="[classes, props.class]">
    <slot />
  </div>
</template>

<script setup>
const props = defineProps({
  class: {
    type: String,
    default: '',
  },
});
const classes = 'flex flex-col gap-y-8';
</script>
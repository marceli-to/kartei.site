<template>
  <div :class="[baseClasses, props.class]">
    <slot />
  </div>
</template>
<script setup>
const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});
const baseClasses = 'border-y border-y-graphite min-h-default flex items-center select-none';
</script>
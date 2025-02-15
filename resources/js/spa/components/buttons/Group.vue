<template>
  <div :class="class">
    <slot />
  </div>
</template>
<script setup>
const props = defineProps({
  class: {
    type: String,
    default: 'flex flex-col gap-y-8'
  },
});
</script>
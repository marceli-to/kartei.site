<template>
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 100 100" :class="class">
    <path d="M20,50 L40,70 L80,30" stroke="currentColor" stroke-width="6" fill="none" stroke-linecap="butt" stroke-linejoin="miter" />
  </svg>
</template>
<script setup>
const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});
</script>

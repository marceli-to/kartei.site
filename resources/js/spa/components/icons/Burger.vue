<template>
  <svg width="28" height="20" viewBox="0 0 28 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="classes">
    <path d="M28 17H0V20H28V17Z" fill="currentColor"/>
    <path d="M28 0H0V3H28V0Z" fill="currentColor"/>
    <path d="M28 8.49982H0V11.5003H28V8.49982Z" fill="currentColor"/>
  </svg>
</template>
<script setup>
const props = defineProps({
  classes: {
    type: String,
    default: ''
  }
});
</script>

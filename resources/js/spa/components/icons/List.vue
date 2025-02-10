<template>
  <template v-if="variant === 'square'">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0 0V20H20V0H0ZM19.5 19.5H0.5V0.5H19.5V19.5Z" fill="currentColor"/>
      <path d="M15 9.25H5V10.75H15V9.25Z" fill="currentColor"/>
      <path d="M15 5H5V6.5H15V5Z" fill="currentColor"/>
      <path d="M15 13.5H5V15H15V13.5Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'square',
    validator: value => ['square'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>
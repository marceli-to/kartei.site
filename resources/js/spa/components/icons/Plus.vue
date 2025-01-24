<template>
  <template v-if="variant === 'bold'">
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M5.25 0V5.25H0V6.75H5.25V12H6.75V6.75H12V5.25H6.75V0H5.25Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-else>
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M6.5 0H5.5V12H6.5V0Z" fill="currentColor"/>
      <path d="M12 5.5H0V6.5H12V5.5Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: value => ['default', 'bold'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

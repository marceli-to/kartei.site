<template>
  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M15 13.5H5V15H15V13.5Z" fill="currentColor"/>
    <path d="M0 0V20H20V0H0ZM19.5 19.5H0.5V0.5H19.5V19.5Z" fill="currentColor"/>
    <path d="M12.5 9.00098H10.625V5H9.375V9.00098H7.5L10 12.001L12.5 9.00098Z" fill="currentColor"/>
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

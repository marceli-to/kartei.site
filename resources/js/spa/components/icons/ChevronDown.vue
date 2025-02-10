<template>
  <svg width="16" height="8" viewBox="0 0 16 8" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M14.5865 0L8.00052 6.586L1.41452 0L0.70752 0.707001L8.00052 8L15.2925 0.707001L14.5865 0Z" fill="currentColor"/>
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

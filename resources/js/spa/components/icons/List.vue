<template>
  <svg width="24" height="12" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M4 10H0V12H4V10Z" fill="currentColor"/>
    <path d="M4 5H0V7H4V5Z" fill="currentColor"/>
    <path d="M4 0H0V2H4V0Z" fill="currentColor"/>
    <path d="M24 10H8V12H24V10Z" fill="currentColor"/>
    <path d="M24 5H8V7H24V5Z" fill="currentColor"/>
    <path d="M24 0H8V2H24V0Z" fill="currentColor"/>
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
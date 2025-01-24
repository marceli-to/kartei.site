<template>
  <svg width="24" height="14" viewBox="0 0 24 14" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M6 0.5H0V13.5H6V0.5Z" fill="currentColor"/>
    <path d="M15 0.5H9V13.5H15V0.5Z" fill="currentColor"/>
    <path d="M24 0.5H18V13.5H24V0.5Z" fill="currentColor"/>
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

<template>
  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M0.25 0V20H19.75V0H0.25ZM19.25 19.5H0.75V0.5H19.25V19.5Z" fill="currentColor"/>
    <path d="M9.5 5.5H5.75V9.25H9.5V5.5Z" fill="currentColor"/>
    <path d="M14.75 5.5H11V9.25H14.75V5.5Z" fill="currentColor"/>
    <path d="M9.5 10.75H5.75V14.5H9.5V10.75Z" fill="currentColor"/>
    <path d="M14.75 10.75H11V14.5H14.75V10.75Z" fill="currentColor"/>
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

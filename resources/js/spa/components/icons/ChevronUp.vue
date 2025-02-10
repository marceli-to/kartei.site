<template>
  <svg width="16" height="8" viewBox="0 0 16 8" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class" >
    <path d="M1.41351 8L7.99951 1.414L14.5855 8L15.2925 7.293L7.99951 0L0.70752 7.293L1.41351 8Z" fill="currentColor"/>
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

<template>
  <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M21.75 21.5H14.25C14.25 21.5 10.5 16.788 10.5 13.318C10.5 9.55201 13.858 6.5 18 6.5C22.142 6.5 25.5 9.55201 25.5 13.318C25.5 16.788 21.75 21.5 21.75 21.5ZM21.5 25.5H14.5V23.5H21.5V25.5ZM19.5 29.5H16.5V27.5H19.5V29.5ZM18 0C8.059 0 0 8.059 0 18C0 27.941 8.059 36 18 36C27.941 36 36 27.941 36 18C36 8.059 27.941 0 18 0Z" fill="currentColor"/>
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

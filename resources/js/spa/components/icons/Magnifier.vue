<template>
  <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M1 5C1 2.791 2.791 1 5 1C7.209 1 9 2.791 9 5C9 7.209 7.209 9 5 9C2.791 9 1 7.209 1 5ZM12 11.293L8.855 8.14801C9.56 7.28601 10 6.2 10 5C10 2.239 7.761 0 5 0C2.238 0 0 2.239 0 5C0 7.762 2.238 10 5 10C6.2 10 7.286 9.56001 8.148 8.85501L11.293 12L12 11.293Z" fill="currentColor"/>
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

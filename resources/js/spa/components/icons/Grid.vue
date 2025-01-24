<template>
  <svg width="24" height="14" viewBox="0 0 24 14" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M10.9995 0.5H-0.000488281V6H10.9995V0.5Z" fill="currentColor"/>
    <path d="M24.0005 0.5H12.9995V6H24.0005V0.5Z" fill="currentColor"/>
    <path d="M10.9995 8H-0.000488281V13.5H10.9995V8Z" fill="currentColor"/>
    <path d="M24.0005 8H12.9995V13.5H24.0005V8Z" fill="currentColor"/>
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

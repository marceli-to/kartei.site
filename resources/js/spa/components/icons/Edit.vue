<template>
  <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M18 0C8.0589 0 0 8.0589 0 18C0 27.9412 8.0589 36 18 36C27.9412 36 36 27.9412 36 18C36 8.0589 27.9412 0 18 0ZM12.8735 26.3085L9.16119 26.8389L9.69153 23.1266L10.3986 22.4194L13.5806 25.6014L12.8735 26.3085ZM14.9948 24.1872L11.8129 21.0052L24.5408 8.27734L27.7227 11.4593L14.9948 24.1872Z" fill="currentColor"/>
  </svg>
</template>
<script setup>
const props = defineProps({
  class: {
    type: String,
    default: 'text-graphite'
  }
});
</script>

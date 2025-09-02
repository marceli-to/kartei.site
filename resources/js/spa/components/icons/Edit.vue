<template>
  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
    <path d="M12 0C5.37 0 0 5.37 0 12C0 18.63 5.37 24 12 24C18.63 24 24 18.63 24 12C24 5.37 18.63 0 12 0ZM8.58002 17.54L6.10999 17.89L6.46002 15.42L7.52002 14.36L9.64001 16.48L8.58002 17.54ZM10.7 15.42L8.58002 13.3L16.36 5.52L18.48 7.64L10.7 15.42Z" fill="currentColor" />
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

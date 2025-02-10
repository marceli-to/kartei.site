<template>
  <template v-if="variant === 'square'">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0 0V20H20V0H0ZM19.5 19.5H0.5V0.5H19.5V19.5Z" fill="currentColor"/>
      <path d="M10 15C12.7615 15 15 12.7614 15 10H13.75C13.75 12.071 12.0711 13.75 10 13.75C7.92896 13.75 6.25 12.071 6.25 10C6.25 7.92889 7.92896 6.25 10 6.25H10.5V8L13.5 5.75L10.5 3.5V5H10C7.23859 5 5 7.23859 5 10C5 12.7614 7.23859 15 10 15Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'square',
    validator: value => ['square'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>
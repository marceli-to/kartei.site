<template>
  <template v-if="variant === 'default'">
    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M6.5 0C3.18597 0 0.5 2.68604 0.5 6C0.5 9.31403 3.18597 12 6.5 12C9.81403 12 12.5 9.31403 12.5 6C12.5 2.68604 9.81403 0 6.5 0ZM6.5 11C3.73901 11 1.5 8.76105 1.5 6C1.5 3.23804 3.73901 1 6.5 1C9.26099 1 11.5 3.23804 11.5 6C11.5 8.76105 9.26099 11 6.5 11Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'checked'">
    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M6.5 0C3.18597 0 0.5 2.68604 0.5 6C0.5 9.31403 3.18597 12 6.5 12C9.81403 12 12.5 9.31403 12.5 6C12.5 2.68604 9.81403 0 6.5 0ZM6.5 11C3.73901 11 1.5 8.76105 1.5 6C1.5 3.23804 3.73901 1 6.5 1C9.26099 1 11.5 3.23804 11.5 6C11.5 8.76105 9.26099 11 6.5 11Z" fill="currentColor"/>
      <path d="M6.5 8.5C7.88071 8.5 9 7.38071 9 6C9 4.61929 7.88071 3.5 6.5 3.5C5.11929 3.5 4 4.61929 4 6C4 7.38071 5.11929 8.5 6.5 8.5Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: value => ['default', 'checked'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

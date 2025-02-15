<template>
  <template v-if="variant === 'small'">
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M6.5 0H5.5V12H6.5V0Z" fill="currentColor"/>
      <path d="M12 5.5H0V6.5H12V5.5Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'small-bold'">
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M5.25 0V5.25H0V6.75H5.25V12H6.75V6.75H12V5.25H6.75V0H5.25Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'square'">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0 0V20H20V0H0ZM19.5 19.5H0.5V0.5H19.5V19.5Z" fill="currentColor"/>
      <path d="M9.25 15H10.75V10.75H15V9.25H10.75V5H9.25V9.25H5V10.75H9.25V15Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'rounded'">
    <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M18 0C8.05902 0 0 8.05902 0 18C0 27.941 8.05902 36 18 36C27.941 36 36 27.941 36 18C36 8.05902 27.941 0 18 0ZM28 20H20V28H16V20H8V16H16V8H20V16H28V20Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'small',
    validator: value => ['small', 'small-bold', 'square', 'rounded'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

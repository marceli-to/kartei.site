<template>
  <template v-if="variant === 'fill'">
    <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M7 12C3.08 9.984 0 6.62799 0 4.08899C0 2.88799 0.464993 1.84698 1.34599 1.07898C2.13299 0.392979 3.214 0 4.312 0C5.17 0 6.245 0.30699 7 1.10199C7.755 0.30699 8.83 0 9.688 0C10.786 0 11.867 0.392979 12.654 1.07898C13.534 1.84698 14 2.88799 14 4.08899C14 6.62699 10.92 9.984 7 12Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'outline'">
    <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M7 3.30402C6.318 1.44502 4.547 1.42902 4.312 1.42902C3.569 1.42902 2.844 1.68898 2.322 2.14398C1.754 2.64098 1.465 3.29499 1.465 4.08899C1.465 6.02099 3.772 8.627 7 10.38C10.228 8.627 12.535 6.01999 12.535 4.08899C12.535 3.29499 12.246 2.64098 11.677 2.14398C11.155 1.68898 10.43 1.42902 9.688 1.42902C9.453 1.42902 7.681 1.44502 7 3.30402ZM7 12C3.08 9.984 0 6.62799 0 4.08899C0 2.88799 0.465002 1.84698 1.345 1.07898C2.133 0.392979 3.214 0 4.312 0C5.17 0 6.245 0.30699 7 1.10199C7.755 0.30699 8.83 0 9.688 0C10.786 0 11.867 0.392979 12.653 1.07898C13.534 1.84698 14 2.88799 14 4.08899C14 6.62699 10.92 9.984 7 12Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'fill',
    validator: value => ['fill', 'outline'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

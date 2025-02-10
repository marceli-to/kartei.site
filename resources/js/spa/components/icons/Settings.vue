<template>
  <template v-if="variant === 'default'">
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M6 8.25C4.757 8.25 3.74899 7.243 3.74899 6C3.74899 4.758 4.757 3.75 6 3.75C7.243 3.75 8.24899 4.758 8.24899 6C8.24899 7.243 7.243 8.25 6 8.25ZM11.521 8.48499L12 6.33398L10.22 5.45502L10.122 4.91998L11.348 3.40601L9.94901 1.41998L8.216 2.27502L7.502 1.91699L7.075 0H4.924L4.49699 1.91699L3.783 2.27502L2.05099 1.41998L0.709 3.14502L1.93201 4.71802L1.77901 5.45502L0 6.33398L0.477997 8.48499H2.504L2.91901 9.008L2.467 11.043L4.40199 11.985L5.72301 10.353L6.312 10.351L7.59399 12L9.532 11.043L9.08 9.008L9.495 8.48499H11.521Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'tiny'">
    <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M4 5.5C3.172 5.5 2.5 4.829 2.5 4C2.5 3.173 3.172 2.5 4 2.5C4.829 2.5 5.5 3.173 5.5 4C5.5 4.829 4.829 5.5 4 5.5ZM7.681 5.65698L8 4.22302L6.813 3.63702L6.74899 3.28003L7.56599 2.271L6.633 0.947021L5.478 1.51703L5.002 1.27899L4.717 0H3.283L2.99899 1.27899L2.522 1.51703L1.367 0.947021L0.473007 2.09698L1.289 3.14502L1.187 3.63702L0 4.22302L0.319 5.65698H1.66901L1.94701 6.00598L1.645 7.362L2.935 7.98999L3.815 6.90302L4.20801 6.901L5.063 8L6.355 7.362L6.05299 6.00598L6.33099 5.65698H7.681Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: value => ['default', 'tiny'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>


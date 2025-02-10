<template>
  <template v-if="variant === 'large'">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M21.879 0L12 9.879L2.121 0L0 2.121L9.87801 12.001L0 21.879L2.121 24L12 14.122L21.879 24L24 21.879L14.121 12.001L24 2.121L21.879 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'small'">
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0.707502 1.76865e-05L0.000244141 0.707275L9.2925 9.99953L9.99976 9.29228L0.707502 1.76865e-05Z" fill="currentColor"/>
      <path d="M9.29254 1.43608e-06L0.000305176 9.29224L0.707563 9.99949L9.9998 0.707259L9.29254 1.43608e-06Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'small-bold'">
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M8.94 0L5.00101 3.93903L1.06 0L0 1.05902L3.94 4.99902L0 8.93903L1.06 10L5.00101 6.06L8.94 10L10 8.93903L6.061 4.99902L10 1.05902L8.94 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'tiny'">
    <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M7.293 0L4 3.29303L0.708 0L0 0.70697L3.293 4.00098L0 7.29303L0.708 8L4 4.70697L7.293 8L8 7.29303L4.707 4.00098L8 0.70697L7.293 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'square'">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0 0V20H20V0H0ZM19.5 19.5H0.5V0.5H19.5V19.5Z" fill="currentColor"/>
      <path d="M6.06067 15L10 11.0607L13.9393 15L15 13.9394L11.0607 10L15 6.06067L13.9393 5L10 8.93933L6.06067 5L5 6.06067L8.93933 10L5 13.9394L6.06067 15Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'large',
    validator: value => ['large', 'small', 'small-bold', 'tiny', 'square'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

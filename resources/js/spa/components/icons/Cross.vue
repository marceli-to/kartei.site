<template>
  <template v-if="variant === 'large'">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M21.879 0L12 9.879L2.121 0L0 2.121L9.87801 12.001L0 21.879L2.121 24L12 14.122L21.879 24L24 21.879L14.121 12.001L24 2.121L21.879 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'small'">
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0.707481 -6.51071e-06L0.000244141 0.70723L9.2925 9.99949L9.99974 9.29225L0.707481 -6.51071e-06Z" fill="currentColor"/>
      <path d="M9.29256 -3.54019e-05L0.000305176 9.29222L0.707563 9.99948L9.99982 0.707222L9.29256 -3.54019e-05Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'smallBold'">
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M8.94 0L5.00098 3.939L1.06 0L0 1.05899L3.94 4.99899L0 8.939L1.06 10L5.00098 6.06L8.94 10L10 8.939L6.06097 4.99899L10 1.05899L8.94 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'tiny'">
    <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M7.293 0L4 3.29303L0.708008 0L0 0.70697L3.293 4.00098L0 7.29303L0.708008 8L4 4.70697L7.293 8L8 7.29303L4.707 4.00098L8 0.70697L7.293 0Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'large',
    validator: value => ['large', 'small', 'smallBold', 'tiny'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

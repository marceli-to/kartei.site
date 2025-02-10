<template>
  <template v-if="variant === 'large'">
    <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M3.06052 0L0.939514 2.121L10.8175 12.001L0.939514 21.879L3.06052 24L15.0605 12.001L3.06052 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'small'">
    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0.823975 0.705994L6.11603 5.99899L0.823975 11.293L1.53101 12L7.53003 5.99899L1.53101 0L0.823975 0.705994Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'small-bold'">
    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M1.53101 0L0.469971 1.06L5.409 5.99902L0.469971 10.939L1.53101 12L7.53003 5.99902L1.53101 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'tiny'">
    <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M1.35349 0L0.646484 0.70697L3.93948 4.00098L0.646484 7.29303L1.35349 8L5.35349 4.00098L1.35349 0Z" fill="currentColor"/>
    </svg>
  </template>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'large',
    validator: value => ['large', 'small', 'small-bold', 'tiny'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

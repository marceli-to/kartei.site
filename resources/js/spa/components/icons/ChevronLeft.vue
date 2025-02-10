<template>
  <template v-if="variant === 'small'">
    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M7.53003 0.705994L2.23798 5.99899L7.53003 11.293L6.823 12L0.823975 5.99899L6.823 0L7.53003 0.705994Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'small-bold'">
    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M6.46899 0L7.53003 1.06L2.591 5.99902L7.53003 10.939L6.46899 12L0.469971 5.99902L6.46899 0Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'large'">
    <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M12.9395 0L15.0605 2.121L5.18251 12.001L15.0605 21.879L12.9395 24L0.939514 12.001L12.9395 0Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'large',
    validator: value => ['large', 'small', 'small-bold'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

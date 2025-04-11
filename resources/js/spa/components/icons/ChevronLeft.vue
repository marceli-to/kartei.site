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
  <template v-if="variant === 'large-rounded'">
    <svg width="19" height="32" viewBox="0 0 19 32" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M15.6172 0.158249C15.8282 -0.0527496 16.1712 -0.0527496 16.3822 0.158249L18.4443 2.22124C18.6562 2.43224 18.6562 2.77428 18.4443 2.98528L5.43224 16.0002L18.4452 29.0132C18.6562 29.2253 18.6562 29.5663 18.4452 29.7782L16.3812 31.8412C16.1702 32.0522 15.8282 32.0522 15.6163 31.8412L0.158249 16.3832C-0.0527496 16.1722 -0.0527496 15.8293 0.158249 15.6183L15.6172 0.158249Z" fill="currentColor"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'large',
    validator: value => ['large', 'large-rounded', 'small', 'small-bold'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>

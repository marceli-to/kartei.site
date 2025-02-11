<template>
  <template v-if="variant === 'default'">
    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0 0V32H32V0H0ZM1.5 1.5H30.5V30.5H1.5V1.5Z" fill="currentColor"/>
      <path d="M10.5 17L13.5 21.8L19 13L29 29H3L10.5 17Z" fill="currentColor"/>
      <path d="M7.5 10.5C9.15685 10.5 10.5 9.15685 10.5 7.5C10.5 5.84315 9.15685 4.5 7.5 4.5C5.84315 4.5 4.5 5.84315 4.5 7.5C4.5 9.15685 5.84315 10.5 7.5 10.5Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'missing'">
    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M11.5 14.5C13.1569 14.5 14.5 13.1569 14.5 11.5C14.5 9.84315 13.1569 8.5 11.5 8.5C9.84315 8.5 8.5 9.84315 8.5 11.5C8.5 13.1569 9.84315 14.5 11.5 14.5Z" fill="currentColor"/>
      <path d="M5.5 5.5H31.318L32.818 4H4V32.818L5.5 31.318V5.5Z" fill="currentColor"/>
      <path d="M34.5 34.5H8.68201L7.18201 36H36V7.18201L34.5 8.68201V34.5Z" fill="currentColor"/>
      <path d="M38.9393 0.000366211L0.000305176 38.9394L1.06128 40.0004L39.9993 1.0614V1.05939L38.9393 0.000366211Z" fill="currentColor"/>
      <path d="M17.697 25.4848L17.5 25.7998L17.454 25.7268L10.182 32.9998H33L24.224 18.9578L17.697 25.4848Z" fill="currentColor"/>
    </svg>
  </template>
  <template v-if="variant === 'square'">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="class">
      <path d="M0 0V20H20V0H0ZM0.5 0.5H19.5V19.5H0.5V0.5Z" fill="black"/>
      <path d="M7.88464 10.3846L9.03845 12.2308L11.1539 8.84619L15 15H5L7.88464 10.3846Z" fill="black"/>
      <path d="M6.73077 7.30774C7.36804 7.30774 7.88464 6.79113 7.88464 6.15387C7.88464 5.51661 7.36804 5 6.73077 5C6.09351 5 5.5769 5.51661 5.5769 6.15387C5.5769 6.79113 6.09351 7.30774 6.73077 7.30774Z" fill="black"/>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: value => ['default', 'missing', 'square'].includes(value)
  },
  class: {
    type: String,
    default: 'text-graphite'
  }
});
</script>

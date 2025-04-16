<template>
  <template v-if="variant === 'square'">
    <svg fill="none" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg">
      <g fill="currentColor">
        <path d="m.25 0v20h19.5v-20zm19 19.5h-18.5v-19h18.5z"/>
        <path d="m9.5 5.5h-3.75v3.75h3.75z"/>
        <path d="m14.75 5.5h-3.75v3.75h3.75z"/>
        <path d="m9.5 10.75h-3.75v3.75h3.75z"/>
        <path d="m14.75 10.75h-3.75v3.75h3.75z"/>
      </g>
    </svg>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'square',
    validator: value => ['square'].includes(value)
  },
  class: {
    type: String,
    default: ''
  }
});
</script>
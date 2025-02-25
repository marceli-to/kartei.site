<template>
  <template v-if="variant === 'default'">
    <div class="size-48 flex items-center justify-center" :class="classes">
      <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21.75 21.5H14.25C14.25 21.5 10.5 16.788 10.5 13.318C10.5 9.55201 13.858 6.5 18 6.5C22.142 6.5 25.5 9.55201 25.5 13.318C25.5 16.788 21.75 21.5 21.75 21.5ZM21.5 25.5H14.5V23.5H21.5V25.5ZM19.5 29.5H16.5V27.5H19.5V29.5ZM18 0C8.059 0 0 8.059 0 18C0 27.941 8.059 36 18 36C27.941 36 36 27.941 36 18C36 8.059 27.941 0 18 0Z" fill="currentColor"/>
      </svg>
    </div>
  </template>
  <template v-if="variant === 'active'">
    <div class="size-48 flex items-center justify-center" :class="classes">
      <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M24 6C14.059 6 6 14.059 6 24C6 33.941 14.059 42 24 42C33.941 42 42 33.941 42 24C42 14.059 33.941 6 24 6ZM25.5 35.5H22.5V33.5H25.5V35.5ZM27.5 31.5H20.5V29.5H27.5V31.5ZM27.75 27.5H20.25C20.25 27.5 16.5 22.788 16.5 19.318C16.5 15.552 19.858 12.5 24 12.5C28.142 12.5 31.5 15.552 31.5 19.318C31.5 22.788 27.75 27.5 27.75 27.5Z" fill="currentColor"/>
        <path d="M24 0C10.7453 0 0 10.7453 0 24C0 37.2546 10.7453 48 24 48C37.2546 48 48 37.2546 48 24C48 10.7453 37.2546 0 24 0ZM24 45C12.4022 45 3 35.5978 3 24C3 12.4022 12.4022 3 24 3C35.5978 3 45 12.4022 45 24C45 35.5978 35.5978 45 24 45Z" fill="currentColor"/>
      </svg>
    </div>
  </template>
</template>
<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: value => ['default', 'active'].includes(value)
  },
  classes: {
    type: String,
    default: ''
  }
});
</script>

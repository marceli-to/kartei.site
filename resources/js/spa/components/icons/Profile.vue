<template>
  <template v-if="variant === 'default'">
    <div class="size-48 flex items-center justify-center" :class="classes">
      <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M18 0C8.05884 0 0 8.05884 0 18C0 27.9411 8.05884 36 18 36C27.9411 36 36 27.9411 36 18C36 8.05884 27.9411 0 18 0ZM18 7C20.4853 7 22.5 9.01471 22.5 11.5C22.5 13.9852 20.4853 16 18 16C15.5147 16 13.5 13.9852 13.5 11.5C13.5 9.01471 15.5147 7 18 7ZM18 31C13.4174 31 9.39697 28.6231 7.08191 25.0408C8.49127 21.9437 11.1738 19.5495 14.4625 18.5331C15.3675 19.4393 16.6182 20 18 20C19.3818 20 20.6325 19.4393 21.5375 18.5331C24.8263 19.5494 27.5087 21.9436 28.9181 25.0407C26.603 28.6231 22.5826 31 18 31Z" fill="currentColor"/>
      </svg>
    </div>
  </template>
  <template v-if="variant === 'active'">
    <div class="size-48 flex items-center justify-center" :class="classes">
      <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M24 6C14.059 6 6 14.059 6 24C6 33.941 14.059 42 24 42C33.941 42 42 33.941 42 24C42 14.059 33.941 6 24 6ZM24 13C26.485 13 28.5 15.015 28.5 17.5C28.5 19.985 26.485 22 24 22C21.515 22 19.5 19.985 19.5 17.5C19.5 15.015 21.515 13 24 13ZM24 37C19.417 37 15.397 34.623 13.082 31.041C14.491 27.944 17.174 25.549 20.463 24.533C21.368 25.439 22.618 26 24 26C25.382 26 26.633 25.439 27.538 24.533C30.826 25.549 33.509 27.944 34.918 31.041C32.603 34.623 28.583 37 24 37Z" fill="currentColor"/>
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
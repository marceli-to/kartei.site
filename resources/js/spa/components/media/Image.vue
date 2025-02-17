<template>
  <div :class="[classes, spacing]">
    <img
      :src="src"
      :alt="alt"
      :width="width"
      :height="height"
      class="max-w-full max-h-full object-contain"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  classes: {
    type: String,
    default: 'absolute inset-0 flex justify-center items-center'
  },
  spacing: {
    type: String,
    default: 'm-20'
  },
  width: {
    type: String,
    default: 'auto'
  },
  height: {
    type: String,
    default: 'auto'
  }
});
</script>
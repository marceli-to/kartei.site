<template>
  <button 
    :type="type" 
    :class="classes" 
    :disabled="disabled"
    :aria-label="label">
    {{ label }}
    <slot />
  </button>
</template>
<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'submit'
  },
  label: {
    type: String,
    required: true
  },
  classes: {
    type: String,
    default: 'border border-pebble min-h-default font-muoto-regular text-md text-graphite w-full flex items-center justify-center !ring-0 !outline-none hover:bg-ice hover:text-black transition-all'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  submitting: {
    type: Boolean,
    default: true
  }
});
</script>
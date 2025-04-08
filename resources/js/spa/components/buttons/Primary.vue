<template>
  <button 
    :type="type" 
    :class="[
      classes,
      variant === 'danger' && 'hover:bg-flame hover:text-white'
    ]" 
    :disabled="disabled"
    :aria-label="label">
    {{ label }}
    <slot />
  </button>
</template>
<script setup>
import { computed } from 'vue';

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
    default: 'bg-white dark:bg-black border border-pebble min-h-default font-muoto-regular text-md text-graphite w-full flex items-center justify-center !ring-0 !outline-none hover:text-black transition-all hover:theme-color hover:border-black disabled:hover:text-graphite disabled:hover:bg-transparent disabled:opacity-40 disabled:pointer-events-none'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  submitting: {
    type: Boolean,
    default: true
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'danger'].includes(value)
  }
});

</script>
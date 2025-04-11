<template>
  <button 
    :type="type" 
    :class="computedClasses"
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
    default: 'bg-white dark:bg-black border border-pebble text-graphite hover:text-black transition-all hover:theme-color hover:border-black disabled:hover:text-graphite disabled:hover:bg-transparent disabled:opacity-40 disabled:pointer-events-none'
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

const defaultClasses = 'min-h-default w-full flex items-center justify-center !ring-0 !outline-none';
const dangerClasses = 'bg-white dark:bg-black text-flame hover:bg-flame hover:text-white border-flame border';

const computedClasses = computed(() => {
  if (props.variant === 'danger') {
    return defaultClasses + ' ' + dangerClasses;
  }
  return defaultClasses + ' ' + props.classes;
});

</script>
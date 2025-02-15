<template>
  <button 
    :type="type" 
    :class="class" 
    :disabled="disabled"
    :aria-label="label">
    <IconChevronRight variant="small" />
    {{ label }}
  </button>
</template>
<script setup>
import IconChevronRight from '@/components/icons/ChevronRight.vue';

const props = defineProps({
  type: {
    type: String,
    default: 'submit'
  },
  label: {
    type: String,
    required: true
  },
  class: {
    type: String,
    default: 'border border-graphite min-h-default font-muoto-regular text-md text-white w-full flex items-center justify-start gap-x-12 px-8 bg-pebble hover:bg-ice hover:text-black !ring-0 !outline-none transition-all'
  },
  disabled: {
    type: Boolean,
    default: false
  }
});
</script>
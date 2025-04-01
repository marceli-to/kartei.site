<template>
  <nav :class="navigationClass">
    <div :class="variantClass">
      <slot name="title"></slot>
      <slot name="navigationTitle"></slot>
      <slot name="navigation"></slot>
    </div>
  </nav>
</template>

<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: ''
  }
});

const navigationClass = 'w-2/12 sticky shrink-0 left-0 mt-58 min-h-full z-50 border-graphite border-r bg-white dark:bg-black';

const variantClass = computed(() => {
  return props.variant === 'box' ? 'border border-graphite min-h-[calc(100%_-_20px)] mr-8 pl-8' : '';
});
</script>

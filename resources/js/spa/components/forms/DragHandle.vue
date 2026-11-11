<template>
  <span
    class="absolute top-1/2 right-4 flex -translate-y-1/2 cursor-grab items-center p-4 active:cursor-grabbing"
    :class="handleClass"
    aria-hidden="true">
    <!-- Grip for a vuedraggable list: icon 8px from the field's right edge, the remove cross sits at right-40 -->
    <Icon name="drag" />
  </span>
</template>
<script setup>
import Icon from '@/components/icons/Icon.vue';

defineProps({
  // The class the draggable's `handle` option points at
  handleClass: {
    type: String,
    default: 'drag-handle',
  },
});
</script>

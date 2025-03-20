<template>
  <button @click="$emit('toggle')" :class="['text-sm mb-4 block']">
    <slot></slot>
  </button>
</template>

<script setup>
defineProps({
  isAscending: {
    type: Boolean,
    default: true
  }
});

defineEmits(['toggle']);
</script>
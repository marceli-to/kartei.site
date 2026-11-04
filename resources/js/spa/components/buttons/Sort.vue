<template>
  <button
    type="button"
    :aria-label="isAscending ? 'Sortierung: aufsteigend (A–Z), umkehren' : 'Sortierung: absteigend (Z–A), umkehren'"
    class="mb-4 block text-sm"
    @click="$emit('toggle')">
    <slot></slot>
  </button>
</template>

<script setup>
defineProps({
  isAscending: {
    type: Boolean,
    default: true,
  },
});

defineEmits(['toggle']);
</script>

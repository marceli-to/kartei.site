<template>
  <button
    type="button"
    @click="$emit('toggle')"
    :aria-label="isAscending ? 'Sortierung: aufsteigend (A–Z), umkehren' : 'Sortierung: absteigend (Z–A), umkehren'"
    class="text-sm mb-4 block">
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

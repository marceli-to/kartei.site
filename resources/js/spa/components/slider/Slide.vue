<template>
  <!-- Fills its column in the slider down to pb-20 above the bottom edge (layout: Slider.vue) -->
  <div
    class="relative flex-grow px-8 pb-20"
    :class="{ '-mt-24': pull }">
    <slot />
  </div>
</template>
<script setup>
defineProps({
  // The slide starts with a label above the content line (forms); pulls it up by the label's height
  pull: {
    type: Boolean,
    default: false,
  },
});
</script>

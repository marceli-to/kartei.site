<template>
  <!-- Decorative: the button or link around it carries the accessible name -->
  <svg
    v-if="icon"
    :width="icon.width"
    :height="icon.height"
    :viewBox="icon.viewBox"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
    v-html="icon.body" /><!-- eslint-disable-line vue/no-v-html -- static SVG from registry.js -->
</template>
<script setup>
import { computed } from 'vue';
import icons from '@/components/icons/registry';

/**
 * <Icon name="cross" variant="small" class="..." />
 * Names and variants: see registry.js. class and other attributes go on the <svg>.
 * v-html is safe here: the bodies are our own static SVG markup from registry.js.
 */
const props = defineProps({
  name: {
    type: String,
    required: true,
    validator: (value) => value in icons,
  },
  variant: {
    type: String,
    default: null,
  },
});

const icon = computed(() => {
  const entry = icons[props.name];
  if (!entry) return null;
  return entry.variants[props.variant ?? entry.default] ?? entry.variants[entry.default];
});
</script>

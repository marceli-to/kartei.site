<template>
  <div :class="[
      defaultClass,
      classes
    ]">
    <div class="relative">
      <button
        type="button"
        @click="infoIcon.close()"
        class="absolute right-0 top-0"
        aria-label="Hinweis schliessen"
        v-if="closable">
        <IconCross variant="small" />
      </button>
      <slot />
    </div>
  </div>
</template>
<script setup>
import IconCross from '@/components/icons/Cross.vue';
import { useInfoIconStore } from '@/stores/info';
const infoIcon = useInfoIconStore();

const props = defineProps({
  classes: {
    type: [String],
    default: ''
  },
  defaultClass: {
    type: [String],
    default: 'absolute top-24 -right-8 z-[100] translate-x-full w-10/12 bg-[var(--theme-color)] p-8 text-sm'
  },
  closable: {
    type: Boolean,
    default: true
  }
});
</script>
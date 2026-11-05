<template>
  <div :class="[defaultClass, classes]">
    <div class="relative">
      <button
        v-if="closable"
        type="button"
        class="absolute top-0 right-0"
        aria-label="Hinweis schliessen"
        @click="infoIcon.close()">
        <Icon
          name="cross"
          variant="small" />
      </button>
      <slot />
    </div>
  </div>
</template>
<script setup>
import { useInfoIconStore } from '@/stores/info';
import Icon from '@/components/icons/Icon.vue';
const infoIcon = useInfoIconStore();

defineProps({
  classes: {
    type: [String],
    default: '',
  },
  defaultClass: {
    type: [String],
    default: 'absolute top-24 -right-8 z-100 translate-x-full w-10/12 bg-(--theme-color) p-8 text-sm',
  },
  closable: {
    type: Boolean,
    default: true,
  },
});
</script>

<template>
  <div v-if="infoIcon.isActive" :class="[
      defaultClass,
      classes
    ]">
    <div class="relative">
      <a 
        href=""
        @click.prevent="infoIcon.setActive(false)"
        class="absolute right-0 top-0"
        v-if="closable">
        <IconCross variant="small" />
      </a>
      <slot />
    </div>
  </div>
</template>
<script setup>
import IconCross from '@/components/icons/Cross.vue';
import { useInfoIconStore } from '@/components/infobox/stores/info';
const infoIcon = useInfoIconStore();

const props = defineProps({
  classes: {
    type: [String],
    default: ''
  },
  defaultClass: {
    type: [String],
    default: 'absolute top-24 -right-8 z-[100] translate-x-full w-10/12 bg-ice p-8 text-sm'
  },
  closable: {
    type: Boolean,
    default: true
  }
});
</script>
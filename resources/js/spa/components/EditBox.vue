<template>
  <!-- An entry opened for editing: a box headed by its name. Title and cross sit like the sidebar's «Voreinstellungen»
       header: title 7px below the outer edge, cross centred on the title line, 9px from the right outer edge (border included).
       fill: the box takes the column height and holds the form's buttons too -->
  <div
    class="relative border border-graphite px-8"
    :class="fill ? 'flex min-h-full flex-col pb-8' : 'pb-32'">
    <div class="flex min-h-default items-start pt-6 pr-32">
      <span class="font-muoto-regular text-md">{{ title }}</span>
      <button
        type="button"
        aria-label="Schliessen"
        title="Schliessen"
        class="absolute top-13 right-8"
        @click="$emit('close')">
        <Icon
          name="cross"
          variant="small" />
      </button>
    </div>
    <div
      class="mt-20"
      :class="{ 'flex grow flex-col': fill }">
      <slot />
    </div>
  </div>
</template>

<script setup>
import Icon from '@/components/icons/Icon.vue';

defineProps({
  title: {
    type: String,
    required: true,
  },
  fill: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['close']);
</script>

<template>
  <!-- An entry opened for editing: a box headed by its name, with a cross 8px from the outer edge (border included).
       fill: the box takes the column height and holds the form's buttons too -->
  <div
    class="relative border border-graphite px-8"
    :class="fill ? 'flex min-h-full flex-col pb-8' : 'pb-32'">
    <div class="flex min-h-default items-center pr-32">
      <span class="font-muoto-regular text-md">{{ title }}</span>
      <button
        type="button"
        aria-label="Schliessen"
        title="Schliessen"
        class="absolute top-7 right-7"
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

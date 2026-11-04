<template>
  <fieldset
    :class="{
      'flex flex-col gap-y-8': variant === 'default',
      'flex flex-col first-of-type:border-t first-of-type:border-t-graphite': variant === 'reduced',
    }">
    <legend
      v-if="legend"
      class="sr-only">
      {{ legend }}<template v-if="error">: {{ error }}</template>
    </legend>
    <label
      v-for="option in options"
      :key="option.value"
      :class="computedClasses">
      <span :class="labelClasses">{{ option.label }}</span>
      <div class="relative flex items-center justify-center">
        <input
          type="radio"
          :name="name"
          :value="option.value"
          :checked="modelValue === option.value"
          :aria-invalid="error ? 'true' : undefined"
          class="sr-only"
          @change="$emit('update:modelValue', option.value)" />
        <Icon
          v-if="modelValue !== option.value"
          name="radio" />
        <Icon
          v-else
          name="radio"
          variant="checked" />
      </div>
    </label>
  </fieldset>
</template>

<script setup>
import { computed } from 'vue';
import Icon from '@/components/icons/Icon.vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  // Name of the group for screen readers (the visible InputLabel sits outside the fieldset)
  legend: {
    type: String,
    default: '',
  },
  // Server message; the visible one is in the InputLabel, this one is for screen readers
  error: {
    type: String,
    default: null,
  },
  options: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every((option) => typeof option === 'object' && 'value' in option && 'label' in option);
    },
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'reduced'].includes(value),
  },
  classes: {
    type: String,
    default: '',
  },
  labelClasses: {
    type: String,
    default: 'font-muoto-regular text-md select-none',
  },
});

const computedClasses = computed(() => {
  // The radio itself is sr-only, so the row shows the keyboard focus
  const baseClasses =
    'min-h-default pr-8 flex items-center justify-between cursor-pointer group has-[:focus-visible]:outline has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-black dark:has-[:focus-visible]:outline-white';
  const variantClasses = props.variant === 'default' ? 'border-y border-graphite' : 'border-b border-graphite';
  return `${baseClasses} ${variantClasses} ${props.classes}`;
});

defineEmits(['update:modelValue']);
</script>

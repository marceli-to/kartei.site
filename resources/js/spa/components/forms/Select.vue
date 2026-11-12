<template>
  <div :class="wrapperClasses">
    <select
      v-bind="$attrs"
      :id="id"
      :value="modelValue"
      :aria-invalid="error ? 'true' : undefined"
      :class="[selectClasses, { 'border-flame! bg-flame! text-white!': error }]"
      @change="onChange">
      <option
        v-if="placeholder"
        disabled
        value="">
        {{ placeholder }}
      </option>

      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value">
        {{ option.label }}
      </option>
    </select>
    <div :class="[chevronClasses, { 'text-white!': props.error }]">
      <Icon name="chevron-down" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Icon from '@/components/icons/Icon.vue';

// aria-label, name ... belong on the select, not the wrapper
defineOptions({ inheritAttrs: false });

const props = defineProps({
  id: {
    type: String,
    default: null,
  },
  modelValue: {
    type: [String, Number],
    required: true,
  },
  placeholder: {
    type: String,
    default: '',
  },
  options: {
    type: Array,
    required: true,
    validator: (options) => {
      return options.every((option) => 'value' in option && 'label' in option);
    },
  },
  classes: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'box'].includes(value),
  },
});

const wrapperClasses = 'relative';

const defaultClasses =
  'w-full min-h-default bg-none! appearance-none text-md ring-0! px-0 py-2 border-x-white focus:border-x-white bg-snow text-black dark:bg-onyx dark:text-white dark:placeholder:text-white dark:border-x-black dark:focus:border-white dark:focus:border-x-black border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black';

const boxClasses =
  'w-full min-h-default bg-none! appearance-none text-md ring-0! px-8 py-2 border border-graphite focus:border-black bg-white dark:bg-black dark:text-white placeholder:font-muoto-italic placeholder:text-black';

const selectClasses = computed(() => {
  const baseClasses = props.variant === 'box' ? boxClasses : defaultClasses;
  return props.classes ? `${baseClasses} ${props.classes}` : baseClasses;
});

const chevronClasses = computed(() => {
  return props.variant === 'box'
    ? 'absolute inset-y-0 right-8 flex items-center pointer-events-none text-black dark:text-white'
    : 'absolute inset-y-0 right-8 flex items-center pointer-events-none text-black dark:text-white';
});

const emit = defineEmits(['update:modelValue', 'update:error']);

const onChange = (event) => {
  emit('update:modelValue', event.target.value);
  if (props.error) emit('update:error', '');
};
</script>

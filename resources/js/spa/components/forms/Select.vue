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
      <!-- small: 12×6 as in the sidebar design -->
      <Icon
        name="chevron-down"
        :class="{ 'h-6 w-12': size === 'small' }" />
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
  // small: 20px high, text-2xs (record list sidebar)
  size: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'small'].includes(value),
  },
});

const wrapperClasses = 'relative';

const defaultClasses =
  'w-full min-h-default bg-none! appearance-none text-md ring-0! px-0 py-2 border-x-white focus:border-x-white bg-snow border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black';

const boxClasses =
  'w-full min-h-default bg-none! appearance-none text-md ring-0! px-8 py-2 border border-graphite focus:border-black bg-white placeholder:font-muoto-italic placeholder:text-black';

const smallClasses = 'block h-20! min-h-0! py-0! text-2xs! leading-none!';

const selectClasses = computed(() => {
  const variantClasses = props.variant === 'box' ? boxClasses : defaultClasses;
  const baseClasses = props.size === 'small' ? `${variantClasses} ${smallClasses}` : variantClasses;
  return props.classes ? `${baseClasses} ${props.classes}` : baseClasses;
});

const chevronClasses = computed(() => {
  return props.variant === 'box'
    ? 'absolute inset-y-0 right-8 flex items-center pointer-events-none text-black'
    : 'absolute inset-y-0 right-8 flex items-center pointer-events-none text-black';
});

const emit = defineEmits(['update:modelValue', 'update:error']);

const onChange = (event) => {
  emit('update:modelValue', event.target.value);
  if (props.error) emit('update:error', '');
};
</script>

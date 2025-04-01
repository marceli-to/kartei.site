<template>
  <div :class="wrapperClasses">
    <select
      :id="id"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      :class="[
        selectClasses,
        { '!bg-flame !border-flame !text-white': error },
      ]">
      <option v-if="placeholder" 
        disabled 
        value=""
        selected="!modelValue">
        {{ placeholder }}
      </option>

      <option 
        v-for="option in options" 
        :key="option.value" 
        :value="option.value">
        {{ option.label }}
      </option>
    </select>
    <div :class="[chevronClasses, { '!text-white': props.error }]">
      <IconChevronDown />
    </div>
  </div>
</template>

<script setup>
import IconChevronDown from '@/components/icons/ChevronDown.vue';
import { computed } from 'vue';

const props = defineProps({
  id: {
    type: String,
    default: null
  },
  modelValue: {
    type: [String, Number],
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    required: true,
    validator: (options) => {
      return options.every(option => 
        'value' in option && 'label' in option
      )
    }
  },
  classes: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'box'].includes(value)
  }
});

const wrapperClasses = 'relative';

const defaultClasses = 'w-full min-h-default !bg-none appearance-none text-md !ring-0 px-0 py-2 border-x-white focus:border-x-white bg-snow border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black';

const boxClasses = 'w-full min-h-default !bg-none appearance-none text-md !ring-0 px-8 py-2 border border-graphite focus:border-black bg-white placeholder:font-muoto-italic placeholder:text-black';

const selectClasses = computed(() => {
  const baseClasses = props.variant === 'box' ? boxClasses : defaultClasses;
  return props.classes ? `${baseClasses} ${props.classes}` : baseClasses;
});

const chevronClasses = computed(() => {
  return props.variant === 'box' 
    ? 'absolute inset-y-0 right-8 flex items-center pointer-events-none text-black'
    : 'absolute inset-y-0 right-8 flex items-center pointer-events-none text-black';
});

defineEmits(['update:modelValue']);
</script>

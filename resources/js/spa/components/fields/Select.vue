<template>
  <div :class="wrapperClasses">
    <select
      :id="id"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      :class="[
        props.classes,
        { '!bg-flame !border-flame !text-white': error },
      ]">
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
const props = defineProps({
  id: {
    type: String,
    default: null
  },
  modelValue: {
    type: [String, Number],
    required: true
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
    default: 'w-full min-h-default !bg-none appearance-none text-md !ring-0 px-0 py-2 border-x-white focus:border-x-white bg-snow border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black'
  },
  error: {
    type: String,
    default: ''
  },
});

const wrapperClasses = 'relative';

const chevronClasses = 'absolute inset-y-0 right-8 flex items-center px-2 pointer-events-none text-black';

defineEmits(['update:modelValue']);
</script>


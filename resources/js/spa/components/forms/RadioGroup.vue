<template>
  <fieldset :class="{
    'flex flex-col gap-y-8': variant === 'default',
    'flex flex-col first-of-type:border-t first-of-type:border-t-graphite': variant === 'reduced',
    }">
    <label 
      :class="computedClasses"
      v-for="option in options" 
      :key="option.value">
      <span :class="labelClasses">{{ option.label }}</span>
      <div class="relative flex items-center justify-center">
        <input
          type="radio"
          :name="name"
          :value="option.value"
          :checked="modelValue === option.value"
          @change="$emit('update:modelValue', option.value)"
          class="sr-only"
        />
        <IconRadio v-if="modelValue !== option.value" />
        <IconRadio variant="checked" v-else />
      </div>
    </label>
  </fieldset>
</template>

<script setup>
import IconRadio from '@/components/icons/Radio.vue';
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    required: true
  },
  name: {
    type: String,
    required: true
  },
  options: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every(option => 
        typeof option === 'object' && 
        'value' in option && 
        'label' in option
      );
    }
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'reduced'].includes(value)
  },
  classes: {
    type: String,
    default: ''
  },
  labelClasses: {
    type: String,
    default: 'font-muoto-regular text-md select-none'
  }
});

const computedClasses = computed(() => {
  const baseClasses = 'min-h-default pr-8 flex items-center justify-between cursor-pointer group';
  const variantClasses = props.variant === 'default' 
    ? 'border-y border-graphite' 
    : 'border-b border-graphite';
  return `${baseClasses} ${variantClasses} ${props.classes}`;
});

defineEmits(['update:modelValue']);
</script>
<template>
  <fieldset class="grid grid-cols-2 gap-8">
    <label 
      v-for="option in options" 
      :key="option.value" 
      class="relative">
      <input
        type="checkbox"
        :name="name"
        :value="option.value"
        :checked="modelValue.includes(option.value)"
        @change="updateValue(option.value)"
        class="peer sr-only"
      />
      <span 
        :class="[
          borderClasses,
          classes,
          modelValue.includes(option.value) ? activeClass : '',
        ]"
      >
        {{ option.label }}
      </span>
    </label>
  </fieldset>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: Array,
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
  classes: {
    type: String,
    default: 'min-h-default w-full flex items-center justify-center font-muoto-regular text-md text-graphite hover:text-black transition-all user-select-none cursor-pointer px-8'
  },
  borderClasses: {
    type: String,
    default: 'border border-graphite'
  },
  activeClass: {
    type: String,
    default: 'bg-ice border-black !text-black'
  }
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (value) => {
  const newValue = [...props.modelValue];
  const index = newValue.indexOf(value);
  
  if (index === -1) {
    newValue.push(value);
  } else {
    newValue.splice(index, 1);
  }
  
  emit('update:modelValue', newValue);
};
</script>
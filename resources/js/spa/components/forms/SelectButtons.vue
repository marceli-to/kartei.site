<template>
  <fieldset :class="wrapperClasses">
    <label 
      v-for="option in options" 
      :key="option.value" 
      class="relative">
      <input
        :type="multiple ? 'checkbox' : 'radio'"
        :name="name"
        :value="option.value"
        :checked="isChecked(option.value)"
        @change="updateValue(option.value)"
        class="peer sr-only"
      />
      <span 
        :class="[
          borderClasses,
          classes,
          isChecked(option.value) ? activeClass : '',
        ]"
      >
        {{ option.label }}
      </span>
    </label>
  </fieldset>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: [Array, String, Number],
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
  multiple: {
    type: Boolean,
    default: false
  },
  classes: {
    type: String,
    default: 'min-h-default w-full flex items-center font-muoto-regular text-md text-graphite hover:text-black transition-all select-none cursor-pointer px-8'
  },
  wrapperClasses: {
    type: String,
    default: ''
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

const isChecked = (value) => {
  return props.multiple 
    ? props.modelValue.includes(value) // For multiple selections
    : props.modelValue === value; // For single selection
};

const updateValue = (value) => {
  if (props.multiple) {
    const newValue = [...props.modelValue];
    const index = newValue.indexOf(value);
    
    if (index === -1) {
      newValue.push(value);
    } 
    else {
      newValue.splice(index, 1);
    }
    emit('update:modelValue', newValue);
  } 
  else {
    emit('update:modelValue', value);
  }
};
</script>
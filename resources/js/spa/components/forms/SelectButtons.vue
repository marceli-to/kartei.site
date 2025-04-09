<template>
  <fieldset :class="wrapperClasses">
    <label 
      v-for="option in options" 
      :key="option.value" 
      :class="[
        labelClasses,
        option.disabled ? 'pointer-events-none' : ''
      ]">
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
          isChecked(option.value) ? getActiveClass(option.value) : '',
          option.disabled ? disabledClasses : ''
        ]"
      >
        <span v-if="option.iconBefore" class="option-icon-before mr-2">
          <slot 
            name="iconBefore" 
            :option="option" 
            :is-checked="isChecked(option.value)"
          ></slot>
        </span>
        {{ option.label }}
        <span class="option-icon">
          <slot 
            name="icon" 
            :option="option" 
            :is-checked="isChecked(option.value)"
          ></slot>
        </span>
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
    default: 'min-h-default w-full flex justify-between items-center text-md text-graphite hover:text-black dark:hover:text-white transition-all select-none cursor-pointer px-8'
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
    default: 'theme-color border-black !text-black'
  },
  labelClasses: {
    type: String,
    default: 'relative'
  },
  disabledClasses: {
    type: String,
    default: '!bg-white !text-black pointer-events-none'
  },
  valueActiveClassMap: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update:modelValue']);

const isChecked = (value) => {
  return props.multiple 
    ? props.modelValue.includes(value) // For multiple selections
    : props.modelValue === value; // For single selection
};

const updateValue = (value) => {
  // Check if the option is disabled before updating
  const optionItem = props.options.find(opt => opt.value === value);
  if (optionItem && optionItem.disabled) {
    return; // Don't update if the option is disabled
  }
  
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

const getActiveClass = (value) => {
  // If there's a specific active class for this value, use it
  if (props.valueActiveClassMap && props.valueActiveClassMap[value]) {
    return props.valueActiveClassMap[value];
  }
  return props.activeClass;
};
</script>
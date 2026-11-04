<template>
  <fieldset
    ref="fieldset"
    :class="wrapperClasses">
    <legend
      v-if="legend"
      class="sr-only">
      {{ legend }}
    </legend>
    <label
      v-for="option in options"
      :key="option.value"
      :class="[labelClasses, option.disabled ? 'pointer-events-none' : '']">
      <input
        :type="multiple ? 'checkbox' : 'radio'"
        :name="name"
        :value="option.value"
        :checked="isChecked(option.value)"
        class="peer sr-only"
        @change="updateValue(option.value)" />
      <span
        :class="[
          borderClasses,
          'flex min-h-default w-full cursor-pointer select-none items-center justify-between px-8 text-md text-graphite transition-all hover:text-black dark:hover:text-white',
          // The input is sr-only, so the button shows the keyboard focus
          'peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-black dark:peer-focus-visible:outline-white',
          classes,
          isChecked(option.value) ? getActiveClass(option.value) : '',
          option.disabled ? disabledClasses : '',
        ]">
        <span
          v-if="option.iconBefore"
          class="option-icon-before mr-2">
          <slot
            name="iconBefore"
            :option="option"
            :is-checked="isChecked(option.value)"></slot>
        </span>
        <template v-if="option.prefix">
          <span>
            <span class="inline-block w-40">
              {{ option.prefix }}
            </span>
            <span :class="option.labelClass ? option.labelClass : ''">
              {{ option.label }}
            </span>
          </span>
        </template>
        <template v-else>
          {{ option.label }}
        </template>
        <span class="option-icon">
          <slot
            name="icon"
            :option="option"
            :is-checked="isChecked(option.value)"></slot>
        </span>
      </span>
    </label>
  </fieldset>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps({
  modelValue: {
    type: [Array, String, Number, Boolean],
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
  options: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every((option) => typeof option === 'object' && 'value' in option && 'label' in option);
    },
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  classes: {
    type: String,
    default: '',
  },
  wrapperClasses: {
    type: String,
    default: 'text-graphite',
  },
  borderClasses: {
    type: String,
    default: 'border border-graphite',
  },
  activeClass: {
    type: String,
    default: 'theme-color !border-black !text-black',
  },
  labelClasses: {
    type: String,
    default: 'relative',
  },
  disabledClasses: {
    type: String,
    default: '!bg-white !text-black pointer-events-none',
  },
  valueActiveClassMap: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update:modelValue']);

const isChecked = (value) => {
  return props.multiple
    ? props.modelValue.includes(value) // For multiple selections
    : props.modelValue === value; // For single selection
};

const fieldset = ref(null);

// The inputs show what modelValue says, so a change the parent declines is undone
const syncInputs = () => {
  fieldset.value?.querySelectorAll('input').forEach((input, index) => {
    input.checked = isChecked(props.options[index].value);
  });
};

const updateValue = (value) => {
  nextTick(syncInputs);

  // Check if the option is disabled before updating
  const optionItem = props.options.find((opt) => opt.value === value);
  if (optionItem && optionItem.disabled) {
    return; // Don't update if the option is disabled
  }

  if (props.multiple) {
    const newValue = [...props.modelValue];
    const index = newValue.indexOf(value);

    if (index === -1) {
      newValue.push(value);
    } else {
      newValue.splice(index, 1);
    }
    emit('update:modelValue', newValue);
  } else {
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

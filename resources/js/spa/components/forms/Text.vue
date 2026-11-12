<template>
  <input
    :id="id"
    :type="type"
    :value="modelValue"
    :maxlength="maxLength"
    :aria-invalid="error ? 'true' : undefined"
    :placeholder="placeholder"
    :disabled="disabled"
    :readonly="readonly"
    :class="[defaultClasses, props.classes, { 'border-flame! bg-flame! placeholder:text-white!': error }]"
    @input="onInput" />
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  id: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  maxLength: {
    type: [String, Number],
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  classes: {
    type: [String, Object, Array],
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
});

// Define default classes
const defaultClasses =
  'w-full min-h-default text-md ring-0! px-0 py-2 border-x-white focus:border-x-white bg-snow text-black dark:bg-onyx dark:text-white dark:placeholder:text-white dark:border-x-black dark:focus:border-white dark:focus:border-x-black border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black disabled:bg-white readonly:bg-white dark:disabled:bg-black dark:readonly:bg-black';

const emit = defineEmits(['update:modelValue', 'update:error']);

// The error stays until the value changes, so it can still be read while fixing it
const onInput = (event) => {
  emit('update:modelValue', event.target.value);
  if (props.error) emit('update:error', '');
};
</script>

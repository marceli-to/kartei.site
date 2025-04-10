<template>
  <textarea
    :id="id"
    :value="modelValue"
    :maxlength="maxLength"
    @input="$emit('update:modelValue', $event.target.value)"
    @focus="$emit('update:error', '')"
    :placeholder="placeholder"
    :disabled="disabled"
    :readonly="readonly"
    :class="[
      defaultClasses,
      props.classes,
      { '!bg-flame !border-flame placeholder:!text-white': error },
    ]"
  ></textarea>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  id: {
    type: String,
    default: ''
  },
  maxLength: {
    type: [String, Number],
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  classes: {
    type: [String, Object, Array],
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  }
});

defineEmits(['update:modelValue', 'update:error']);

const defaultClasses = 'appearance-none w-full text-md !ring-0 min-h-default px-0 py-2 border-x-white focus:border-x-white bg-snow border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black disabled:bg-white';
</script>
<style scoped>
textarea {
  field-sizing: content;
  resize: vertical;
}
</style>

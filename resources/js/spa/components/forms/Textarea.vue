<template>
  <textarea
    :id="id"
    ref="textarea"
    rows="1"
    :value="modelValue"
    :maxlength="maxLength"
    :aria-invalid="error ? 'true' : undefined"
    :placeholder="placeholder"
    :disabled="disabled"
    :readonly="readonly"
    :class="[defaultClasses, props.classes, { 'border-flame! bg-flame! placeholder:text-white!': error }]"
    @input="onInput"></textarea>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  id: {
    type: String,
    default: '',
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

const emit = defineEmits(['update:modelValue', 'update:error']);

// The error stays until the value changes, so it can still be read while fixing it
const onInput = (event) => {
  emit('update:modelValue', event.target.value);
  if (props.error) emit('update:error', '');
};

// Browsers without `field-sizing: content` (Safari) don't grow the field with its text
const textarea = ref(null);
const growsByItself = typeof CSS !== 'undefined' && CSS.supports('field-sizing', 'content');
const fitHeight = () => {
  const element = textarea.value;
  if (growsByItself || !element) return;
  element.style.height = 'auto';
  // scrollHeight leaves out the borders
  element.style.height = `${element.scrollHeight + element.offsetHeight - element.clientHeight}px`;
};
onMounted(fitHeight);
watch(
  () => props.modelValue,
  () => nextTick(fitHeight),
);

const defaultClasses =
  'appearance-none w-full text-md ring-0! min-h-default px-0 py-2 border-x-white focus:border-x-white bg-snow text-black dark:bg-onyx dark:text-white dark:placeholder:text-white dark:border-x-black dark:focus:border-white dark:focus:border-x-black border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black disabled:bg-white dark:disabled:bg-black';
</script>
<style scoped>
textarea {
  field-sizing: content;
  resize: vertical;
  /* field-sizing would otherwise widen the field for a long word or URL */
  max-width: 100%;
  overflow-wrap: anywhere;
}
</style>

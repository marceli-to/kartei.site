<template>
  <div class="relative">
    <input
      v-bind="$attrs"
      :id="id"
      :type="type"
      :value="modelValue"
      @input="onInput"
      :aria-invalid="error ? 'true' : undefined"
      :placeholder="placeholder"
      :class="[
        defaultClasses,
        props.classes,
        { '!bg-flame !border-flame placeholder:!text-white': error },
      ]">
      <button
        type="button"
        @click="toggleType" 
        class="absolute inset-y-0 right-8 flex items-center text-graphite"
        :aria-label="type === 'text' ? 'Passwort verbergen' : 'Passwort anzeigen'"
        :title="type === 'text' ? 'Passwort verbergen' : 'Passwort anzeigen'">
        <span class="w-17 h-16 flex items-center justify-center">
          <Icon name="eye" :variant="type === 'text' ? 'enabled' : 'disabled'" />
        </span>
      </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Icon from '@/components/icons/Icon.vue';

// aria-label, autocomplete, name ... belong on the input, not the wrapper
defineOptions({ inheritAttrs: false });

const type = ref('password');

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  id: {
    type: String,
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
  }
});

function toggleType() {
  type.value = type.value === 'text' ? 'password' : 'text';
}

// Define default classes
const defaultClasses = 'w-full min-h-default text-md !ring-0 px-0 py-2 border-x-white focus:border-x-white bg-snow border-y border-y-graphite focus:border-black placeholder:font-muoto-italic placeholder:text-black';

const emit = defineEmits(['update:modelValue', 'update:error']);

// The error stays until the value changes, so it can still be read while fixing it
const onInput = (event) => {
  emit('update:modelValue', event.target.value);
  if (props.error) emit('update:error', '');
};
</script>
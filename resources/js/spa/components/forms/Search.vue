<template>
  <div class="relative">
    <input
      v-bind="$attrs"
      :id="id || undefined"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :class="[defaultClasses, props.classes]"
      @input="$emit('update:modelValue', $event.target.value)" />
    <template v-if="modelValue">
      <button
        type="button"
        class="absolute top-1/2 right-8 -translate-y-1/2 text-black"
        aria-label="Suchfeld leeren"
        title="Suchfeld leeren"
        @click="$emit('update:modelValue', '')">
        <Icon
          name="cross"
          variant="small"
          :class="{ 'size-10': size === 'small' }" />
      </button>
    </template>
    <template v-else>
      <Icon
        name="magnifier"
        aria-hidden="true"
        class="absolute top-1/2 right-8 -translate-y-1/2 text-black"
        :class="{ 'size-10': size === 'small' }" />
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Icon from '@/components/icons/Icon.vue';

// aria-label etc. belong on the input, not the wrapper
defineOptions({ inheritAttrs: false });

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
  placeholder: {
    type: String,
    default: '',
  },
  classes: {
    type: [String, Object, Array],
    default: '',
  },
  // small: 20px high, text-2xs (record list sidebar)
  size: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'small'].includes(value),
  },
});

// Define default classes
const baseClasses =
  'w-full ring-0! px-8 bg-transparent border border-graphite dark:placeholder:text-white focus:border-black placeholder:text-black';
const defaultClasses = computed(() =>
  props.size === 'small' ? `${baseClasses} h-20 py-0 text-2xs` : `${baseClasses} min-h-default py-2 text-md`,
);

// Define emits
defineEmits(['update:modelValue']);
</script>

<template>
  <div class="relative">
    <input
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :placeholder="placeholder"
      :class="[
        defaultClasses,
        props.classes,
      ]"
    >
    <template v-if="modelValue">
      <a 
        href="javascript:;"
        @click.prevent="$emit('update:modelValue', '')"
        title="Suchfeld leeren">
        <IconCross variant="small" class="absolute right-8 top-1/2 -translate-y-1/2 text-black" />
      </a>
    </template>
    <template v-else>
      <IconMagnifier class="absolute right-8 top-1/2 -translate-y-1/2 text-black" />
    </template>
  </div>
</template>

<script setup>
import IconMagnifier from '@/components/icons/Magnifier.vue';
import IconCross from '@/components/icons/Cross.vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  id: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
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

// Define default classes
const defaultClasses = 'w-full min-h-default text-md !ring-0 px-8 py-2 bg-transparent border border-graphite dark:placeholder:text-white focus:border-black placeholder:text-black';

// Define emits
defineEmits(['update:modelValue', 'update:error']);
</script>
<template>
  <div class="relative">
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :placeholder="placeholder"
      :class="[
        defaultClasses,
        props.classes,
        { '!bg-flame !border-flame placeholder:!text-white': error },
      ]">
      <a 
        href="javascript:;"
        @click="toggleType" 
        class="absolute inset-y-0 right-8 flex items-center text-graphite"
        title="Passwort anzeigen">
        <template v-if="type === 'text'">
          <IconEye variant="enabled" />
        </template>
        <template v-else>
          <IconEye variant="disabled" />
        </template>
      </a>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import IconEye from '@/components/icons/Eye.vue';

const type = ref('text');

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

defineEmits(['update:modelValue', 'update:error']);
</script>
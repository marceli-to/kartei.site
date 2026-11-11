<template>
  <div class="flex items-center gap-x-17">
    <!-- One category or register row: number, title (with remove and drag handle), ID -->
    <div class="w-3/12">
      <InputStatic :class="strong ? 'font-muoto-medium' : ''">{{ number }}</InputStatic>
    </div>
    <div class="relative w-6/12">
      <InputText
        v-model="name"
        :placeholder="placeholder"
        :classes="['pr-56', strong ? 'font-muoto-medium' : ''].join(' ')"
        :aria-label="`${label} ${position} Titel`"
        @blur="$emit('blur')" />
      <button
        type="button"
        class="absolute top-1/2 right-40 -translate-y-1/2"
        :aria-label="`${label} ${position} entfernen`"
        @click="$emit('remove')">
        <Icon
          name="cross"
          variant="small" />
      </button>
      <DragHandle :handle-class="handleClass" />
    </div>
    <div class="w-3/12">
      <InputText
        v-model="customId"
        :aria-label="`${label} ${position} ID`"
        :readonly="readonly" />
    </div>
  </div>
</template>
<script setup>
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import Icon from '@/components/icons/Icon.vue';
import DragHandle from '@/components/forms/DragHandle.vue';

// vuedraggable item: keep a single root element (no comment above it, or dev mode sees a fragment and drops fail).
// Title and ID of a category or register: v-model:name, v-model:custom-id
const name = defineModel('name', { type: String, default: '' });
const customId = defineModel('customId', { type: String, default: '' });

defineProps({
  number: {
    type: String,
    required: true,
  },
  // "Kategorie" or "Register", for the accessible names
  label: {
    type: String,
    required: true,
  },
  // 1-based position in its list
  position: {
    type: Number,
    required: true,
  },
  placeholder: {
    type: String,
    default: '',
  },
  // Category rows: number and title in medium
  strong: {
    type: Boolean,
    default: false,
  },
  // Categories and registers get their own handle class, so the outer list never picks up a register's handle
  handleClass: {
    type: String,
    required: true,
  },
  // The ID is derived from the title (ID type "auto")
  readonly: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['blur', 'remove']);
</script>

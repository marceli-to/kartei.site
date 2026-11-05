<template>
  <!-- One category or register row: number, title (with remove button), ID -->
  <div class="drag-handle flex cursor-move items-center gap-x-17">
    <div class="w-3/12">
      <InputStatic>{{ number }}</InputStatic>
    </div>
    <div class="relative w-6/12">
      <InputText
        v-model="name"
        :placeholder="placeholder"
        :aria-label="`${label} ${position} Titel`"
        @blur="$emit('blur')" />
      <button
        type="button"
        class="absolute top-12 right-8"
        :aria-label="`${label} ${position} entfernen`"
        @click="$emit('remove')">
        <Icon
          name="cross"
          variant="small" />
      </button>
    </div>
    <div class="w-3/12">
      <InputText
        v-model="customId"
        :aria-label="`${label} ${position} Kürzel`"
        :readonly="readonly" />
    </div>
  </div>
</template>
<script setup>
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import Icon from '@/components/icons/Icon.vue';

// Title and Kürzel of a category or register: v-model:name, v-model:custom-id
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
  // The ID is derived from the title (ID type "auto")
  readonly: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['blur', 'remove']);
</script>

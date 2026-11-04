<template>
  <!-- One category or register row: number, title (with remove button), ID -->
  <div class="flex gap-x-17 items-center drag-handle cursor-move">
    <div class="w-3/12">
      <InputStatic>{{ number }}</InputStatic>
    </div>
    <div class="w-6/12 relative">
      <InputText
        v-model="row.name"
        :placeholder="placeholder"
        :aria-label="`${label} ${position} Titel`"
        @blur="$emit('blur')" />
      <button 
        type="button" 
        class="absolute right-8 top-12"
        :aria-label="`${label} ${position} entfernen`"
        @click="$emit('remove')">
        <IconCross variant="small" />
      </button>
    </div>
    <div class="w-3/12">
      <InputText
        v-model="row.custom_id"
        :aria-label="`${label} ${position} Kürzel`"
        :readonly="readonly" />
    </div>
  </div>
</template>
<script setup>
import IconCross from '@/components/icons/Cross.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';

// row is the category or register object of the structure form, edited in place
defineProps({
  row: {
    type: Object,
    required: true
  },
  number: {
    type: String,
    required: true
  },
  // "Kategorie" or "Register", for the accessible names
  label: {
    type: String,
    required: true
  },
  // 1-based position in its list
  position: {
    type: Number,
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  // The ID is derived from the title (ID type "auto")
  readonly: {
    type: Boolean,
    default: false
  }
});

defineEmits(['blur', 'remove']);
</script>

<template>
  <div
    class="flex flex-col justify-between min-h-full"
    :class="disabled ? 'opacity-20 pointer-events-none transition-opacity' : ''">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputSearch
          v-model="localSearchQuery"
          placeholder="Suche"
          aria-label="Suche"
          classes="!min-h-slim !py-0" />
      </InputGroup>

      <InputGroup>
        <InputLabel label="Sortierfolge" id="sort_order" class="font-muoto-medium" />
        <InputSelect
          id="sort_order"
          v-model="localSortOrder"
          :options="sortOrderOptions"
          variant="box"
          classes="!min-h-slim !py-0"
        />
      </InputGroup>

      <InputGroup v-if="categories.length > 0">
        <InputLabel label="Kategorien" id="category" class="font-muoto-medium" />
        <InputSelectButtons
          v-model="localSelectedCategories"
          :multiple="true"
          name="categories"
          :options="categories"
          classes="!text-black !min-h-slim"
          wrapperClasses="flex flex-col gap-y-8" />
      </InputGroup>

      <InputGroup v-if="registers.length > 0">
        <InputLabel label="Register" id="register" class="font-muoto-medium" />
        <InputSelectButtons
          v-model="localSelectedRegisters"
          :multiple="true"
          name="registers"
          :options="registers"
          classes="!text-black !min-h-slim"
          wrapperClasses="flex flex-col gap-y-8" />
      </InputGroup>

      <InputGroup v-if="tags.length > 0">
        <InputLabel label="Tags" id="tags" class="font-muoto-medium" />
        <InputSelectButtons
          v-model="localSelectedRegisters"
          :multiple="true"
          name="tags"
          :options="tags"
          classes="!text-black !min-h-slim"
          wrapperClasses="flex flex-col gap-y-8" />
      </InputGroup>

    </div>

    <InputGroup class="sticky bottom-20">
      <Action
        label="Voreinstellungen"
        variant="box"
        :to="{ name: 'archiveSettings', params: { uuid } }"
        :icon="{ name: 'Settings', position: 'right' }" />
    </InputGroup>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

import InputSearch from '@/components/forms/Search.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputGroup from '@/components/forms/Group.vue';
import Action from '@/components/buttons/Action.vue';

const props = defineProps({
  uuid: { type: String, required: true },
  disabled: Boolean,
  modelValue: Object,
  categories: {
    type: Array,
    default: () => []
  },
  registers: {
    type: Array,
    default: () => []
  },
  tags: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue']);

const localSearchQuery = ref(props.modelValue?.searchQuery || '');
const localSortOrder = ref(props.modelValue?.sortOrder || 'name');
const localSelectedCategories = ref(props.modelValue?.selectedCategories || []);
const localSelectedRegisters = ref(props.modelValue?.selectedRegisters || []);
const localSelectedTags = ref(props.modelValue?.selectedTags || []);

const sortOrderOptions = [
  { value: 'name', label: 'Name' },
  { value: 'date', label: 'Datum' },
  { value: 'created_at', label: 'Erstellt' },
  { value: 'updated_at', label: 'Aktualisiert' }
];

watch(
  [localSearchQuery, localSortOrder, localSelectedCategories, localSelectedRegisters, localSelectedTags],
  () => {
    emit('update:modelValue', {
      searchQuery: localSearchQuery.value,
      sortOrder: localSortOrder.value,
      selectedCategories: localSelectedCategories.value,
      selectedRegisters: localSelectedRegisters.value,
      selectedTags: localSelectedTags.value
    });
  },
  { immediate: true }
);
</script>

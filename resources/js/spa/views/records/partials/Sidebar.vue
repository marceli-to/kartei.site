<template>
  <div
    class="flex min-h-full flex-col justify-between"
    :class="disabled ? 'pointer-events-none opacity-20 transition-opacity' : ''">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputSearch
          v-model="localSearchQuery"
          placeholder="Suche"
          aria-label="Suche" />
      </InputGroup>

      <InputGroup>
        <InputLabel
          id="sort_order"
          label="Sortierfolge" />
        <InputSelect
          id="sort_order"
          v-model="localSortOrder"
          :options="sortOrderOptions"
          variant="box" />
      </InputGroup>

      <InputGroup v-if="categories.length > 0">
        <InputLabel
          id="category"
          label="Kategorien" />
        <InputSelectButtons
          v-model="localSelectedCategories"
          legend="Kategorien"
          :multiple="true"
          name="categories"
          :options="categories"
          classes="text-black!"
          wrapper-classes="flex flex-col gap-y-8" />
      </InputGroup>

      <InputGroup v-if="visibleRegisters.length > 0">
        <InputLabel
          id="register"
          label="Register" />
        <InputSelectButtons
          v-model="localSelectedRegisters"
          legend="Register"
          :multiple="true"
          name="registers"
          :options="visibleRegisters"
          classes="text-black!"
          wrapper-classes="flex flex-col gap-y-8" />
      </InputGroup>

      <InputGroup v-if="tags.length > 0">
        <InputLabel
          id="tags"
          label="Tags" />
        <InputSelectButtons
          v-model="localSelectedTags"
          legend="Tags"
          :multiple="true"
          name="tags"
          :options="tags"
          classes="text-black!"
          wrapper-classes="flex flex-col gap-y-8" />
      </InputGroup>
    </div>

    <InputGroup
      v-if="canEditSettings"
      class="sticky bottom-20">
      <Action
        label="Voreinstellungen"
        variant="box"
        :to="{ name: 'archiveSettings', params: { uuid } }"
        :icon="{ name: 'Settings', position: 'right' }" />
    </InputGroup>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useUserStore } from '@/stores/user';

import InputSearch from '@/components/forms/Search.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputGroup from '@/components/forms/Group.vue';
import Action from '@/components/buttons/Action.vue';

const props = defineProps({
  uuid: { type: String, required: true },
  disabled: Boolean,
  modelValue: { type: Object, required: true },
  categories: {
    type: Array,
    default: () => [],
  },
  registers: {
    type: Array,
    default: () => [],
  },
  tags: {
    type: Array,
    default: () => [],
  },
});

const userStore = useUserStore();
const canEditSettings = computed(() =>
  userStore.canArchive(props.uuid, [
    'update',
    'manageStructure',
    'manageTemplate',
    'manageTags',
    'manageUsers',
    'delete',
  ]),
);

const emit = defineEmits(['update:modelValue']);

const localSearchQuery = ref(props.modelValue?.searchQuery || '');
const localSortOrder = ref(props.modelValue?.sortOrder || 'name');
const localSelectedCategories = ref(props.modelValue?.selectedCategories || []);
const localSelectedRegisters = ref(props.modelValue?.selectedRegisters || []);
const localSelectedTags = ref(props.modelValue?.selectedTags || []);

// Selected categories show only their own registers, which then narrow the
// category down (category → register → result)
const visibleRegisters = computed(() =>
  localSelectedCategories.value.length > 0
    ? props.registers.filter((register) => localSelectedCategories.value.includes(register.category))
    : props.registers,
);

// Registers that get hidden are deselected too
watch(visibleRegisters, (visible) => {
  const kept = localSelectedRegisters.value.filter((uuid) => visible.some((register) => register.value === uuid));
  if (kept.length !== localSelectedRegisters.value.length) localSelectedRegisters.value = kept;
});

const sortOrderOptions = [
  { value: 'name', label: 'Name' },
  { value: 'date', label: 'Datum' },
  { value: 'created_at', label: 'Erstellt' },
  { value: 'updated_at', label: 'Aktualisiert' },
];

watch(
  [localSearchQuery, localSortOrder, localSelectedCategories, localSelectedRegisters, localSelectedTags],
  () => {
    emit('update:modelValue', {
      searchQuery: localSearchQuery.value,
      sortOrder: localSortOrder.value,
      selectedCategories: localSelectedCategories.value,
      selectedRegisters: localSelectedRegisters.value,
      selectedTags: localSelectedTags.value,
    });
  },
  { immediate: true },
);
</script>

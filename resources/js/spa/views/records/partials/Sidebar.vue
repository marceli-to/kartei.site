<template>
  <!--
    Sticky: stays 20px below the window top while the cards scroll, and always ends 32px above the
    window bottom (height set in fitHeight), so "Voreinstellungen" sits there. Filters that don't
    fit scroll inside the sidebar instead of running under the button.
  -->
  <div
    ref="root"
    class="sticky top-20 flex flex-col justify-between gap-y-20"
    :class="disabled ? 'pointer-events-none opacity-20 transition-opacity' : ''"
    :style="height ? { height: `${height}px` } : null">
    <div class="-mr-8 flex min-h-0 flex-col gap-y-20 overflow-y-auto pr-8">
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
      class="shrink-0">
      <Action
        label="Voreinstellungen"
        variant="box"
        :to="{ name: 'archiveSettings', params: { uuid } }"
        :icon="{ name: 'Settings', position: 'right' }" />
    </InputGroup>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue';
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

// From the sidebar's top (its place in the page, or 20px once it sticks) to 32px above the window bottom
const BOTTOM_GAP = 32;
const root = ref(null);
const height = ref(0);
let frame = 0;
const fitHeight = () => {
  cancelAnimationFrame(frame);
  frame = requestAnimationFrame(() => {
    if (!root.value) return;
    height.value = Math.max(0, Math.floor(window.innerHeight - root.value.getBoundingClientRect().top - BOTTOM_GAP));
  });
};
onMounted(() => {
  fitHeight();
  window.addEventListener('scroll', fitHeight, { passive: true });
  window.addEventListener('resize', fitHeight);
});
onBeforeUnmount(() => {
  cancelAnimationFrame(frame);
  window.removeEventListener('scroll', fitHeight);
  window.removeEventListener('resize', fitHeight);
});

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

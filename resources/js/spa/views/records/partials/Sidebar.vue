<template>
  <!--
    Sticky: stays 20px below the window top while the cards scroll. "Voreinstellungen" is fixed 32px
    above the window bottom (left/width taken from the sidebar); the filter list ends 20px above it
    (height set in fitHeight) and scrolls inside when it doesn't fit.
    Layout as in Benedikt's KAR0001_Sidebar_Layout_261108: 20px fields in text-2xs (11px, tracked), medium labels,
    8px between fields, 22px between groups (24-25px to the next label's capitals, as measured in the PDF); a 4px scroll bar 10px right of the fields (mr-16 / -mr-14 pr-10)
    and a fade-out at the bottom while there is more to scroll.
  -->
  <div
    ref="root"
    class="sticky top-20 mr-16 flex flex-col"
    :class="disabled ? 'pointer-events-none opacity-20 transition-opacity' : ''"
    :style="height ? { height: `${height}px` } : null">
    <div class="relative -mr-14 flex min-h-0 flex-col">
      <div
        ref="filters"
        class="filters flex min-h-0 flex-col gap-y-22 overflow-y-auto pr-10"
        @scroll.passive="updateScrollState">
        <InputGroup>
          <InputSearch
            v-model="localSearchQuery"
            size="small"
            placeholder="Suchen"
            aria-label="Suchen" />
        </InputGroup>

        <InputGroup>
          <InputLabel
            id="sort_order"
            label="Sortierfolge"
            :classes="LABEL_CLASSES" />
          <InputSelect
            id="sort_order"
            v-model="localSortOrder"
            :options="sortOrderOptions"
            variant="box"
            size="small" />
        </InputGroup>

        <InputGroup v-if="categories.length > 0">
          <InputLabel
            id="category"
            label="Kategorien"
            :classes="LABEL_CLASSES" />
          <InputSelectButtons
            v-model="localSelectedCategories"
            legend="Kategorien"
            :multiple="true"
            name="categories"
            :options="categories"
            size="small"
            classes="text-black!"
            wrapper-classes="flex flex-col gap-y-8" />
        </InputGroup>

        <InputGroup v-if="visibleRegisters.length > 0">
          <InputLabel
            id="register"
            label="Register"
            :classes="LABEL_CLASSES" />
          <InputSelectButtons
            v-model="localSelectedRegisters"
            legend="Register"
            :multiple="true"
            name="registers"
            :options="visibleRegisters"
            size="small"
            classes="text-black!"
            wrapper-classes="flex flex-col gap-y-8" />
        </InputGroup>

        <InputGroup v-if="tags.length > 0">
          <InputLabel
            id="tags"
            label="Tags"
            :classes="LABEL_CLASSES" />
          <InputSelectButtons
            v-model="localSelectedTags"
            legend="Tags"
            :multiple="true"
            name="tags"
            :options="tags"
            size="small"
            classes="text-black!"
            wrapper-classes="flex flex-col gap-y-8" />
        </InputGroup>
      </div>
      <!-- Fade-out over the fields only (not the scroll bar), while there is more to scroll -->
      <div
        v-if="canScrollDown"
        class="pointer-events-none absolute right-14 bottom-0 left-0 h-84 bg-linear-to-b from-transparent to-white dark:to-black"></div>
    </div>

    <InputGroup
      v-if="canEditSettings"
      ref="button"
      class="fixed bottom-32 z-20"
      :style="{ left: `${box.left}px`, width: `${box.width}px` }">
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

const LABEL_CLASSES = 'mb-7 block font-muoto-medium text-2xs leading-none';

// Fade the bottom of the filter list while there is more to scroll
const filters = ref(null);
const canScrollDown = ref(false);
const updateScrollState = () => {
  const el = filters.value;
  canScrollDown.value = !!el && el.scrollTop + el.clientHeight < el.scrollHeight - 1;
};
let resizeObserver = null;

// From the sidebar's top (its place in the page, or 20px once it sticks) to 32px above the window bottom
const BOTTOM_GAP = 32; // button to window bottom
const BUTTON_GAP = 20; // filter list to button
const root = ref(null);
const button = ref(null);
const height = ref(0);
const box = ref({ left: 0, width: 0 });
// Synchronous on scroll, so the list and the fixed button never drift apart for a frame
const fitHeight = () => {
  if (!root.value) return;
  const rect = root.value.getBoundingClientRect();
  box.value = { left: rect.left, width: rect.width };
  const buttonHeight = button.value ? button.value.$el.offsetHeight + BUTTON_GAP : 0;
  height.value = Math.max(0, Math.floor(window.innerHeight - rect.top - BOTTOM_GAP - buttonHeight));
  updateScrollState();
};
onMounted(() => {
  fitHeight();
  // Filters appear and disappear (registers of other categories), so watch the list's size
  resizeObserver = new ResizeObserver(updateScrollState);
  if (filters.value) {
    resizeObserver.observe(filters.value);
    for (const child of filters.value.children) resizeObserver.observe(child);
  }
  window.addEventListener('scroll', fitHeight, { passive: true });
  window.addEventListener('resize', fitHeight);
});
onBeforeUnmount(() => {
  resizeObserver?.disconnect();
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
<style scoped>
/* Keep the scroll bar's 4px (styled globally in app.css) even when nothing scrolls, so the fields keep their width */
.filters {
  scrollbar-gutter: stable;
}
</style>

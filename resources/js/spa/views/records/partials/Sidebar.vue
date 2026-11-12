<template>
  <!--
    Sticky: stays 20px below the window top while the cards scroll. "Voreinstellungen" is fixed 32px
    above the window bottom (left/width taken from the sidebar); the filter list ends 20px above it
    (height set in fitHeight) and scrolls inside when it doesn't fit.
    Fields, labels and spacing in the regular form sizes (decided against the smaller ones of the sidebar design, Stackr #125);
    the scroll bar sits 10px right of the fields (mr-16, fields as wide as the sidebar) and a fade-out at the bottom
    while there is more to scroll. The bar's width is measured: Safari may draw its own wider bar instead of the
    styled 4px one, which used to cut the fields off on the right and add a horizontal bar.
  -->
  <div
    ref="root"
    class="sticky top-20 mr-16 flex flex-col"
    :class="disabled ? 'pointer-events-none opacity-20 transition-opacity' : ''"
    :style="height ? { height: `${height}px` } : null">
    <div
      class="relative flex min-h-0 flex-col"
      :style="{ marginRight: `-${GAP + scrollbarWidth}px` }">
      <div
        ref="filters"
        class="filters flex min-h-0 flex-col overflow-x-hidden overflow-y-auto"
        @scroll.passive="updateScrollState">
        <!-- Exactly the sidebar's width (like «Voreinstellungen»), with or without a scroll bar: browsers that
             show it only while the list overflows (Safari) would otherwise change the field width -->
        <div
          class="flex flex-col gap-y-20"
          :style="box.width ? { width: `${box.width}px` } : null">
          <InputGroup>
            <InputSearch
              v-model="localSearchQuery"
              placeholder="Suchen"
              aria-label="Suchen" />
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
              classes="text-black! dark:text-white!"
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
              classes="text-black! dark:text-white!"
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
              classes="text-black! dark:text-white!"
              wrapper-classes="flex flex-col gap-y-8" />
          </InputGroup>
        </div>
      </div>
      <!-- Fade-out over the fields only (not the scroll bar), while there is more to scroll -->
      <div
        v-if="canScrollDown"
        class="pointer-events-none absolute bottom-0 left-0 h-84 bg-linear-to-b from-transparent to-white dark:to-black"
        :style="{ right: `${GAP + scrollbarWidth}px` }"></div>
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

// Fade the bottom of the filter list while there is more to scroll
const filters = ref(null);
const canScrollDown = ref(false);
const GAP = 10; // fields to scroll bar
const scrollbarWidth = ref(4);
const updateScrollState = () => {
  const el = filters.value;
  if (el) scrollbarWidth.value = el.offsetWidth - el.clientWidth;
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

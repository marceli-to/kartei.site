<template>
  <div class="relative flex w-full grow">
    <Actions :links="viewLinks" />
    <div class="mt-106 flex min-h-full w-full">
      <ContentNavigation>
        <div class="flex flex-col gap-y-20">
          <div class="flex flex-col gap-y-8">
            <InputGroup>
              <InputSearch
                v-model="searchQuery"
                placeholder="Suchen"
                aria-label="Suchen" />
            </InputGroup>
            <InputGroup
              v-if="userStore.can('create.archive')"
              class="relative">
              <Action
                label="Erstellen"
                variant="box"
                :to="{ name: 'archiveSettings' }"
                :icon="{ name: 'Plus', position: 'right' }" />
              <template v-if="isInfoOpen">
                <InfoBox
                  class="absolute top-0! -right-16! flex min-h-default w-auto! items-center py-0!"
                  :closable="false">
                  <InfoCreateArchive />
                </InfoBox>
              </template>
            </InputGroup>
            <InputGroup>
              <Action
                label="Duplizieren"
                variant="box"
                :icon="{ name: 'duplicate', variant: 'small', position: 'right' }" />
            </InputGroup>
          </div>
          <InputGroup>
            <InputLabel
              id="sort_order"
              label="Sortierfolge" />
            <InputSelect
              id="sort_order"
              v-model="sortOrder"
              :options="sortOrderOptions"
              variant="box" />
          </InputGroup>
        </div>
      </ContentNavigation>
      <ContentMain>
        <!--
          From lg up a 10-column grid (16px gutters) like the design: tiles and images span 3 columns
          (three per row, the 10th column stays empty), table rows 2 + 3 columns.
        -->
        <div
          v-if="filteredArchives.length > 0"
          class="grid grid-cols-1 gap-x-16 md:grid-cols-2 lg:grid-cols-10"
          :class="viewGap">
          <Teaser
            v-for="archive in filteredArchives"
            :key="archive.uuid"
            :archive="archive"
            :view="layout"
            :class="viewItemClasses" />
        </div>
        <div
          v-else-if="archives.length > 0"
          class="py-20 text-center">
          <p class="text-gray-500">Keine Ergebnisse gefunden.</p>
        </div>
        <div
          v-else-if="loadFailed"
          class="flex flex-col items-center gap-y-20 py-20">
          <p class="text-gray-500">Die Karteien konnten nicht geladen werden.</p>
          <AppButton
            type="button"
            label="Erneut versuchen"
            @click="loadArchives" />
        </div>
      </ContentMain>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useInfoBox } from '@/composables/useInfoBox';
import { usePageTitle } from '@/composables/usePageTitle';
import { useUserStore } from '@/stores/user';
import { getByUser } from '@/services/api/archive';
import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import InputSearch from '@/components/forms/Search.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputGroup from '@/components/forms/Group.vue';
import Action from '@/components/buttons/Action.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import InfoCreateArchive from '@/views/archives/partials/CreateArchiveInfo.vue';
import Teaser from '@/views/archives/partials/Teaser.vue';
import Actions from '@/views/records/partials/Actions.vue';
import AppButton from '@/components/buttons/Button.vue';

const userStore = useUserStore();
const { setTitle } = usePageTitle();
setTitle('Meine Karteien');

const searchQuery = ref('');
const sortOrder = ref('name');

const hasArchive = ref(true);
const archives = ref([]);
const loadFailed = ref(false);

const sortOrderOptions = [
  { value: 'name', label: 'Name' },
  { value: 'date', label: 'Datum' },
  { value: 'created_at', label: 'Erstellt' },
  { value: 'updated_at', label: 'Aktualisiert' },
];

// View toggle above the content, remembered in the browser
const VIEW_STORAGE_KEY = 'kartei.archives.view';
const views = [
  { value: 'tiles', label: 'Kacheln', icon: 'tiles' },
  { value: 'table', label: 'Tabelle', icon: 'list' },
  { value: 'images', label: 'Bilder', icon: 'image' },
];

const storedView = localStorage.getItem(VIEW_STORAGE_KEY);
const layout = ref(views.some((view) => view.value === storedView) ? storedView : 'tiles');
watch(layout, (value) => localStorage.setItem(VIEW_STORAGE_KEY, value));

const viewGap = computed(() => ({ tiles: 'gap-y-32', table: 'gap-y-28', images: 'gap-y-16' })[layout.value]);
const viewItemClasses = computed(() =>
  layout.value === 'table' ? 'md:col-span-2 lg:col-span-5 lg:col-start-1' : 'lg:col-span-3',
);

const viewLinks = computed(() =>
  views.map((view) => ({
    type: 'button',
    label: view.label,
    icon: view.icon,
    active: layout.value === view.value,
    onClick: () => (layout.value = view.value),
  })),
);

// Search by name or Kürzel, in the browser (the list is not paginated)
const filteredArchives = computed(() => {
  const query = searchQuery.value.trim().toLowerCase();
  if (!query) return archives.value;
  return archives.value.filter((archive) =>
    [archive.name, archive.acronym].some((value) => value?.toLowerCase().includes(query)),
  );
});

// Points to "Erstellen" while there is no archive yet
const { isOpen: isInfoOpen } = useInfoBox({
  active: () => !hasArchive.value,
  auto: true,
});

const loadArchives = async () => {
  loadFailed.value = false;
  try {
    // initialize() retries when the permissions failed to load before
    await userStore.initialize();
    if (!userStore.user?.uuid) {
      loadFailed.value = true;
      return;
    }

    archives.value = await getByUser(userStore.user.uuid);
    hasArchive.value = archives.value.length > 0;
  } catch {
    // Shown in the page (with a retry button), no toast on top
    loadFailed.value = true;
  }
};

onMounted(loadArchives);
</script>

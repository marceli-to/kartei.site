<template>
  <div class="flex w-full grow">
    <div class="mt-106 flex min-h-full w-full">
      <ContentNavigation>
        <div class="flex flex-col gap-y-48">
          <div class="flex flex-col gap-y-20">
            <InputGroup>
              <InputSearch
                v-model="searchQuery"
                placeholder="Suche"
                aria-label="Suche" />
            </InputGroup>
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
            <InputGroup>
              <InputLabel
                id="layout"
                label="Darstellung" />
              <InputSelect
                id="layout"
                v-model="layout"
                :options="layoutOptions"
                variant="box" />
            </InputGroup>
          </div>
          <div class="flex flex-col gap-y-40">
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
                :icon="{ name: 'Plus', position: 'right' }" />
            </InputGroup>
          </div>
        </div>
      </ContentNavigation>
      <ContentMain>
        <div
          v-if="filteredArchives.length > 0"
          class="flex flex-wrap gap-x-16 gap-y-32">
          <Teaser
            v-for="archive in filteredArchives"
            :key="archive.uuid"
            :archive="archive"
            class="w-full shrink-0 md:w-[calc(50%-8px)] lg:w-[calc(33.333%-(32px/3))] 2xl:w-[calc(25%-12px)]" />
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
import { ref, computed, onMounted } from 'vue';
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
import AppButton from '@/components/buttons/Button.vue';

const userStore = useUserStore();
const { setTitle } = usePageTitle();
setTitle('Meine Karteien');

const searchQuery = ref('');
const sortOrder = ref('name');
const layout = ref('list');
const hasArchive = ref(true);
const archives = ref([]);
const loadFailed = ref(false);

const sortOrderOptions = [
  { value: 'name', label: 'Name' },
  { value: 'date', label: 'Datum' },
  { value: 'created_at', label: 'Erstellt' },
  { value: 'updated_at', label: 'Aktualisiert' },
];

const layoutOptions = [
  { value: 'list', label: 'Liste' },
  { value: 'grid', label: 'Gitter' },
];

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

<template>
  <div
    v-if="!isLoading"
    class="relative flex w-full grow">
    <Actions :links="actionLinks">
      <router-link
        v-if="uuid"
        :to="{ name: 'archives' }"
        class="inline-flex items-center gap-x-12"
        aria-label="Zurück zu Meine Karteien">
        <Icon
          name="chevron-left"
          variant="small-bold" />
        <!-- Same type as the slider headings in the settings ("Abonnement", …) -->
        <span class="font-muoto-medium leading-none">Meine Karteien</span>
      </router-link>
    </Actions>
    <div class="mt-106 flex min-h-full w-full">
      <ContentNavigation>
        <Sidebar
          v-model="filters"
          :uuid="uuid"
          :categories="categories"
          :registers="registers"
          :tags="tags">
        </Sidebar>
      </ContentNavigation>

      <ContentMain>
        <template v-if="records.length > 0">
          <div
            class="flex flex-wrap gap-x-16 gap-y-32 transition-opacity"
            :class="{ 'opacity-50': isFiltering }">
            <Card
              v-for="record in records"
              :key="record.uuid"
              :record="record"
              class="w-full shrink-0 md:w-[calc(50%-8px)] lg:w-[calc(33.333%-(32px/3))] 2xl:w-[calc(25%-12px)]" />
          </div>
          <!-- Loads the next page when scrolled into view; stops after a failed request -->
          <div
            v-if="hasMore && !loadMoreFailed"
            ref="loadMoreSentinel"
            class="h-px" />
          <div
            v-if="hasMore && loadMoreFailed"
            class="flex justify-center pt-32">
            <AppButton
              type="button"
              label="Weitere Karten laden"
              @click="retryLoadMore" />
          </div>
        </template>
        <template v-else>
          <div v-if="archiveTotal === 0">
            <router-link
              v-if="canCreate"
              :to="{ name: 'archiveRecordCreate', params: { uuid } }"
              aria-label="Erste Karte erstellen">
              <Skeleton />
            </router-link>
            <Skeleton v-else />
          </div>
          <div
            v-else
            class="py-20 text-center">
            <p class="text-gray-500">Keine Ergebnisse gefunden.</p>
          </div>
        </template>
      </ContentMain>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { usePageTitle } from '@/composables/usePageTitle';
import { useArchiveMeta } from '@/views/records/composables/useArchiveMeta';
import { getArchive } from '@/services/api/archive';
import { getRecords } from '@/services/api/record';
import { notifyError } from '@/services/api/error';

import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import Skeleton from '@/views/records/partials/Skeleton.vue';
import Card from '@/views/records/partials/Card.vue';
import Sidebar from '@/views/records/partials/Sidebar.vue';
import Actions from '@/views/records/partials/Actions.vue';
import Icon from '@/components/icons/Icon.vue';
import AppButton from '@/components/buttons/Button.vue';

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const { categories, registers, tags, loadArchiveMeta } = useArchiveMeta();

const { setTitle } = usePageTitle();

const isLoading = ref(true);
const archive = ref(null);
const records = ref([]);

const userStore = useUserStore();
const canCreate = computed(() => userStore.canArchive(uuid.value, 'createRecord'));

const createLink = {
  type: 'link',
  label: 'Karte',
  icon: 'plus',
  to: { name: 'archiveRecordCreate', params: { uuid: uuid.value } },
  router: true,
};

const actionLinks = computed(() => [...(canCreate.value ? [createLink] : []), ...otherActionLinks]);

// Not built yet: shown greyed out
const otherActionLinks = [
  {
    type: 'button',
    label: 'Ordnen',
    icon: 'reorder',
    disabled: true,
  },
  {
    type: 'button',
    label: 'Kacheln',
    icon: 'tiles',
    disabled: true,
  },
  {
    type: 'button',
    label: 'Liste',
    icon: 'list',
    disabled: true,
  },
  {
    type: 'button',
    label: 'Bilder',
    icon: 'image',
    disabled: true,
  },
  {
    type: 'button',
    label: 'Export',
    icon: 'download',
    disabled: true,
  },
  {
    type: 'button',
    label: 'Teilen',
    icon: 'upload',
    disabled: true,
  },
];

const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: [],
  selectedTags: [],
});

// Filtering, sorting and paging run on the server
const PER_PAGE = 48;
const page = ref(0);
const lastPage = ref(1);
const archiveTotal = ref(null);
const isFiltering = ref(false);
const isLoadingMore = ref(false);
const loadMoreFailed = ref(false);
const hasMore = computed(() => page.value < lastPage.value);

const listParams = () => ({
  search: filters.value.searchQuery.trim() || undefined,
  sort: filters.value.sortOrder,
  categories: filters.value.selectedCategories,
  registers: filters.value.selectedRegisters,
  tags: filters.value.selectedTags,
  per_page: PER_PAGE,
});

// Only the latest request may update the list (filters can change mid-flight).
// A new search also aborts the requests still running for the old one.
let requestId = 0;
let lastParamsKey = null;
let controller = new AbortController();

const fetchPage = (pageNumber) =>
  getRecords(
    uuid.value,
    { ...listParams(), page: pageNumber },
    { meta: { skipLoading: true }, signal: controller.signal },
  );

const abortRunning = () => {
  controller.abort();
  controller = new AbortController();
};

const applyPage = (result, append) => {
  records.value = append ? [...records.value, ...result.data] : result.data;
  page.value = result.meta.current_page;
  lastPage.value = result.meta.last_page;
  archiveTotal.value = result.meta.archive_total;
};

const reload = async () => {
  const key = JSON.stringify(listParams());
  if (key === lastParamsKey) return;
  lastParamsKey = key;

  abortRunning();
  const id = ++requestId;
  isFiltering.value = true;
  try {
    const result = await fetchPage(1);
    if (id === requestId) {
      applyPage(result, false);
      loadMoreFailed.value = false;
    }
  } catch (error) {
    // Let the same filters be tried again
    if (id === requestId) {
      lastParamsKey = null;
      notifyError(error, 'Die Karten konnten nicht geladen werden.');
    }
  } finally {
    if (id === requestId) isFiltering.value = false;
  }
};

const loadMore = async () => {
  if (!hasMore.value || isLoadingMore.value || isFiltering.value) return;
  const id = requestId;
  isLoadingMore.value = true;
  try {
    const result = await fetchPage(page.value + 1);
    if (id === requestId) applyPage(result, true);
  } catch (error) {
    // Re-observing would retry at once and keep failing; wait for the button instead
    if (id === requestId) {
      loadMoreFailed.value = true;
      notifyError(error, 'Weitere Karten konnten nicht geladen werden.');
    }
    return;
  } finally {
    isLoadingMore.value = false;
  }
  // The observer only reports changes: if the sentinel is still in view, check it again
  await nextTick();
  if (loadMoreSentinel.value) {
    observer?.unobserve(loadMoreSentinel.value);
    observer?.observe(loadMoreSentinel.value);
  }
};

const retryLoadMore = () => {
  loadMoreFailed.value = false;
  loadMore();
};

// Wait for a pause in typing before searching; other filters apply at once
let searchTimer = null;
watch(
  () => filters.value.searchQuery,
  () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(reload, 300);
  },
);
watch(
  () => [
    filters.value.sortOrder,
    filters.value.selectedCategories,
    filters.value.selectedRegisters,
    filters.value.selectedTags,
  ],
  () => {
    clearTimeout(searchTimer);
    reload();
  },
  { deep: true },
);

const loadMoreSentinel = ref(null);
let observer = null;

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      if (entries.some((entry) => entry.isIntersecting)) loadMore();
    },
    { rootMargin: '800px' },
  );
});

// The sentinel element comes and goes with v-if
watch(loadMoreSentinel, (element, previous) => {
  if (previous) observer?.unobserve(previous);
  if (element) observer?.observe(element);
});

onUnmounted(() => {
  controller.abort();
  observer?.disconnect();
  clearTimeout(searchTimer);
});

onMounted(async () => {
  try {
    isLoading.value = true;
    await loadData();
  } catch (error) {
    notifyError(error, 'Die Kartei konnte nicht geladen werden.');
  } finally {
    isLoading.value = false;
  }
});

const loadData = async () => {
  lastParamsKey = JSON.stringify(listParams());
  const [archiveData, , recordsData] = await Promise.all([
    getArchive(uuid.value),
    loadArchiveMeta(uuid.value),
    fetchPage(1),
  ]);

  archive.value = archiveData;
  applyPage(recordsData, false);
  setTitle(archive.value.name);
};
</script>

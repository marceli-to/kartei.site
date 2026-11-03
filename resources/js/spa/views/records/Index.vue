<template>
  <div 
    v-if="!isLoading"
    class="flex flex-grow w-full relative">
    <Actions :links="actionLinks">
      <router-link :to="{ name: 'archives' }" v-if="uuid">
        <IconChevronLeft variant="small-bold" />
      </router-link>
    </Actions>
    <div class="flex mt-106 min-h-full w-full">
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
              class="w-full md:w-[calc(50%_-_8px)] lg:w-[calc(33.333%_-_(32px/3))] 2xl:w-[calc(25%_-_12px)] shrink-0"
            />
          </div>
          <!-- Loads the next page when scrolled into view -->
          <div v-if="hasMore" ref="loadMoreSentinel" class="h-px" />
        </template>
        <template v-else>
          <div v-if="archiveTotal === 0">
            <router-link v-if="canCreate" :to="{ name: 'archiveRecordCreate', params: { uuid } }">
              <Skeleton />
            </router-link>
            <Skeleton v-else />
          </div>
          <div v-else class="text-center py-20">
            <p class="text-gray-500">Keine Ergebnisse gefunden.</p>
          </div>       
        </template>
      </ContentMain>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue'
import { useUserStore } from '@/stores/user'
import { useRoute } from 'vue-router'
import { usePageTitle } from '@/composables/usePageTitle'
import { useNormalizeData } from '@/views/records/composables/useNormalizeData'
import { getArchive, getArchiveMeta } from '@/services/api/archive'
import { getRecords } from '@/services/api/record'

import ContentNavigation from '@/components/layout/ContentNavigation.vue'
import ContentMain from '@/components/layout/ContentMain.vue'
import Skeleton from '@/views/records/partials/Skeleton.vue'
import Card from '@/views/records/partials/Card.vue'
import Sidebar from '@/views/records/partials/Sidebar.vue'
import Actions from '@/views/records/partials/Actions.vue'
import IconPlus from '@/components/icons/Plus.vue'
import IconReorder from '@/components/icons/Reorder.vue'
import IconTiles from '@/components/icons/Tiles.vue'
import IconList from '@/components/icons/List.vue'
import IconImage from '@/components/icons/Image.vue'
import IconDownload from '@/components/icons/Download.vue'
import IconUpload from '@/components/icons/Upload.vue'
import IconChevronLeft from '@/components/icons/ChevronLeft.vue'

const route = useRoute()
const uuid = ref(route.params.uuid || null)

const {
  categories,
  categoriesRegisters,
  registers,
  tags,
  normalizeCategoryRegisterData,
  normalizeCategoryData,
  normalizeTagsData
} = useNormalizeData();

const { setTitle } = usePageTitle()

const isLoading = ref(true)
const archive = ref(null)
const records = ref([])

const userStore = useUserStore()
const canCreate = computed(() => userStore.canArchive(uuid.value, 'createRecord'))

const createLink = {
  type: 'link',
  label: 'Karte',
  icon: IconPlus,
  to: { name: 'archiveRecordCreate', params: { uuid: uuid.value } },
  router: true,
}

const actionLinks = computed(() => [
  ...(canCreate.value ? [createLink] : []),
  ...otherActionLinks,
])

const otherActionLinks = [
  {
    type: 'button',
    label: 'Ordnen',
    icon: IconReorder,
    onClick: () => {},
  },
  {
    type: 'button',
    label: 'Kacheln',
    icon: IconTiles,
    onClick: () => {},
  },
  {
    type: 'button',
    label: 'Liste',
    icon: IconList,
    onClick: () => {},
  },
  {
    type: 'button',
    label: 'Bilder',
    icon: IconImage,
    onClick: () => {},
  },
  {
    type: 'button',
    label: 'Export',
    icon: IconDownload,
    onClick: () => {},
  },
  {
    type: 'button',
    label: 'Teilen',
    icon: IconUpload,
    onClick: () => {},
  },
];

const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: [],
  selectedTags: []
})

// Filtering, sorting and paging run on the server
const PER_PAGE = 48
const page = ref(0)
const lastPage = ref(1)
const archiveTotal = ref(null)
const isFiltering = ref(false)
const isLoadingMore = ref(false)
const hasMore = computed(() => page.value < lastPage.value)

const listParams = () => ({
  search: filters.value.searchQuery.trim() || undefined,
  sort: filters.value.sortOrder,
  categories: filters.value.selectedCategories,
  registers: filters.value.selectedRegisters,
  tags: filters.value.selectedTags,
  per_page: PER_PAGE,
})

// Only the latest request may update the list (filters can change mid-flight)
let requestId = 0
let lastParamsKey = null

const fetchPage = (pageNumber) => getRecords(
  uuid.value,
  { ...listParams(), page: pageNumber },
  { meta: { skipLoading: true } }
)

const applyPage = (result, append) => {
  records.value = append ? [...records.value, ...result.data] : result.data
  page.value = result.meta.current_page
  lastPage.value = result.meta.last_page
  archiveTotal.value = result.meta.archive_total
}

const reload = async () => {
  const key = JSON.stringify(listParams())
  if (key === lastParamsKey) return
  lastParamsKey = key

  const id = ++requestId
  isFiltering.value = true
  try {
    const result = await fetchPage(1)
    if (id === requestId) applyPage(result, false)
  }
  catch (error) {
    // Let the same filters be tried again
    if (id === requestId) lastParamsKey = null
    console.error(error)
  }
  finally {
    if (id === requestId) isFiltering.value = false
  }
}

const loadMore = async () => {
  if (!hasMore.value || isLoadingMore.value || isFiltering.value) return
  const id = requestId
  isLoadingMore.value = true
  try {
    const result = await fetchPage(page.value + 1)
    if (id === requestId) applyPage(result, true)
  }
  catch (error) {
    console.error(error)
  }
  finally {
    isLoadingMore.value = false
  }
  // The observer only reports changes: if the sentinel is still in view, check it again
  await nextTick()
  if (loadMoreSentinel.value) {
    observer?.unobserve(loadMoreSentinel.value)
    observer?.observe(loadMoreSentinel.value)
  }
}

// Wait for a pause in typing before searching; other filters apply at once
let searchTimer = null
watch(() => filters.value.searchQuery, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(reload, 300)
})
watch(
  () => [filters.value.sortOrder, filters.value.selectedCategories, filters.value.selectedRegisters, filters.value.selectedTags],
  () => {
    clearTimeout(searchTimer)
    reload()
  },
  { deep: true }
)

const loadMoreSentinel = ref(null)
let observer = null

onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    if (entries.some(entry => entry.isIntersecting)) loadMore()
  }, { rootMargin: '800px' })
})

// The sentinel element comes and goes with v-if
watch(loadMoreSentinel, (element, previous) => {
  if (previous) observer?.unobserve(previous)
  if (element) observer?.observe(element)
})

onUnmounted(() => {
  observer?.disconnect()
  clearTimeout(searchTimer)
})

onMounted(async () => {
  try {
    isLoading.value = true
    await loadData()
  } 
  catch (error) {
    console.error(error)
  } 
  finally {
    isLoading.value = false
  }
})

const loadData = async () => {
  lastParamsKey = JSON.stringify(listParams())
  const [archiveData, archiveMetaData, recordsData] = await Promise.all([
      getArchive(uuid.value),
      getArchiveMeta(uuid.value),
      fetchPage(1)
    ]);

    normalizeCategoryRegisterData({
      categories: archiveMetaData.categories,
      registers: archiveMetaData.registers
    });
    normalizeTagsData(archiveMetaData.tags);
    normalizeCategoryData(archiveMetaData.categories_registers);

    archive.value = archiveData;
    applyPage(recordsData, false);

    // Set title
    setTitle(archive.value.name)
}
</script>
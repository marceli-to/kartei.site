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
          <div class="flex flex-wrap gap-x-16 gap-y-32">
            <Card
              v-for="(record, index) in records"
              :key="record.uuid"
              :record="record"
              class="w-full md:w-[calc(50%_-_8px)] lg:w-[calc(33.333%_-_(32px/3))] 2xl:w-[calc(25%_-_12px)] shrink-0"
            />
          </div>
        </template>
        <template v-else>
          <router-link :to="{ name: 'archiveRecordCreate', params: { uuid } }">
            <Skeleton />
          </router-link>         
        </template>
      </ContentMain>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
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

const isLoading = ref(false)
const archive = ref(null)
const records = ref([])

const actionLinks = [
  {
    type: 'link',
    label: 'Karte',
    icon: IconPlus,
    to: { name: 'archiveRecordCreate', params: { uuid: uuid.value } },
    router: true,
  },
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
  const [archiveData, archiveMetaData, recordsData] = await Promise.all([
      getArchive(uuid.value),
      getArchiveMeta(uuid.value),
      getRecords(uuid.value)
    ]);

    normalizeCategoryRegisterData({
      categories: archiveMetaData.categories,
      registers: archiveMetaData.registers
    });
    normalizeTagsData(archiveMetaData.tags);
    normalizeCategoryData(archiveMetaData.categories_registers);

    archive.value = archiveData;
    records.value = recordsData;

    // Set title
    setTitle(archive.value.name)
}
</script>
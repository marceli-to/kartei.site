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
        <template v-if="filteredRecords.length > 0">
          <div class="flex flex-wrap gap-x-16 gap-y-32">
            <Card
              v-for="(record, index) in filteredRecords"
              :key="record.uuid"
              :record="record"
              class="w-full md:w-[calc(50%_-_8px)] lg:w-[calc(33.333%_-_(32px/3))] 2xl:w-[calc(25%_-_12px)] shrink-0"
            />
          </div>
        </template>
        <template v-else>
          <div v-if="records.length === 0">
            <router-link :to="{ name: 'archiveRecordCreate', params: { uuid } }">
              <Skeleton />
            </router-link>
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
import { ref, onMounted, computed } from 'vue'
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
const categoryHierarchy = ref([])

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

// Helper to get all register UUIDs for selected categories
const getAllowedCategoryUuids = computed(() => {
  const allowed = new Set()
  
  // Add directly selected categories
  filters.value.selectedCategories.forEach(categoryUuid => {
    allowed.add(categoryUuid)
    
    // Find this category in hierarchy and add all its registers
    const category = categoryHierarchy.value.find(cat => cat.uuid === categoryUuid)
    if (category?.registers) {
      category.registers.forEach(register => {
        allowed.add(register.uuid)
      })
    }
  })
  
  // Add directly selected registers
  filters.value.selectedRegisters.forEach(registerUuid => {
    allowed.add(registerUuid)
  })
  
  return Array.from(allowed)
})

// Client-side filtering logic
const filteredRecords = computed(() => {
  let filtered = [...records.value]

  // Search filter
  if (filters.value.searchQuery) {
    const query = filters.value.searchQuery.toLowerCase()
    filtered = filtered.filter(record => 
      record.display_number?.toLowerCase().includes(query) ||
      record.fields?.some(field => 
        field.content?.toLowerCase().includes(query)
      )
    )
  }

  // Category + Register filter (hierarchical)
  if (filters.value.selectedCategories.length > 0 || filters.value.selectedRegisters.length > 0) {
    const allowedUuids = getAllowedCategoryUuids.value
    filtered = filtered.filter(record => 
      allowedUuids.includes(record.category?.uuid)
    )
  }

  // Tags filter
  if (filters.value.selectedTags.length > 0) {
    filtered = filtered.filter(record =>
      record.tags?.some(tagUuid => 
        filters.value.selectedTags.includes(tagUuid)
      )
    )
  }

  // Sort
  filtered.sort((a, b) => {
    switch(filters.value.sortOrder) {
      case 'date':
        return new Date(b.created_at) - new Date(a.created_at)
      case 'created_at':
        return new Date(b.created_at) - new Date(a.created_at)
      case 'updated_at':
        return new Date(b.updated_at) - new Date(a.updated_at)
      case 'name':
      default:
        return a.display_number?.localeCompare(b.display_number) || 0
    }
  })

  return filtered
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

    // Store hierarchical category data for filtering
    categoryHierarchy.value = archiveMetaData.categories_registers;

    archive.value = archiveData;
    records.value = recordsData;

    // Set title
    setTitle(archive.value.name)
}
</script>
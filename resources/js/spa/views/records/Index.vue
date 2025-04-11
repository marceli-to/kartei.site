<template>
  <div 
    v-if="!isLoading"
    class="flex flex-grow w-full">
    <div class="flex mt-106 min-h-full w-full">
      <ContentNavigation>
        <RecordsNavigation
          v-model="filters"
          :uuid="uuid"
          :categories="categories"
          :registers="registers"
          :tags="tags"
        />
      </ContentNavigation>

      <ContentMain>
        <template v-if="records.length > 0">
          <div class="flex flex-wrap gap-x-16 gap-y-32">
            [records]
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
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePageTitle } from '@/composables/usePageTitle';
import { getArchive } from '@/services/api/archive';
import { getTags } from '@/services/api/tags';
import { getStructureCategories } from '@/services/api/archiveStructure';

import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import Skeleton from '@/views/records/partials/Skeleton.vue';
import RecordsNavigation from '@/views/records/partials/Navigation.vue';

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const { setTitle } = usePageTitle();

const isLoading = ref(false);
const archive = ref(null);
const records = ref([]);

const categories = ref([]);
const registers = ref([]);
const tags = ref([]);

const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: [],
  selectedTags: []
});

onMounted(async () => {
  try {
    isLoading.value = true;

    const [archiveData, structure, tags] = await Promise.all([
      getArchive(uuid.value),
      getStructureCategories(uuid.value),
      getTags(uuid.value)
    ]);

    normalizeStructureData(structure);
    normalizeTagsData(tags);

    archive.value = archiveData;
    setTitle(archive.value.name);

  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
  }
});

const normalizeStructureData = (structureData) => {
  categories.value = structureData.categories.map(cat => ({
    label: cat.title,
    value: cat.uuid
  }));

  registers.value = structureData.registers.map(reg => ({
    label: reg.title,
    value: reg.uuid
  }));
};

const normalizeTagsData = (tagsData) => {
  tags.value = tagsData.data.map(tag => ({
    label: tag.name,
    value: tag.uuid
  }));
};
</script>


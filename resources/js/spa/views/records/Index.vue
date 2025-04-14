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
        <router-link :to="{ name: 'archiveRecordCreate', params: { uuid } }">
          Erstellen
        </router-link>
      </ContentNavigation>

      <ContentMain>
        <template v-if="records.length > 0">
          <div class="flex flex-wrap gap-x-16 gap-y-32">
            <Card
              v-for="record, index in records"
              :key="record.uuid"
              :record="record"
              :loopIndex="index"
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
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePageTitle } from '@/composables/usePageTitle';
import { getArchive } from '@/services/api/archive';
import { getTags } from '@/services/api/tags';
import { getRecords } from '@/services/api/record';
import { getCategoriesAndRegisters } from '@/services/api/category';
import { useNormalizeData } from '@/views/records/composables/useNormalizeData';

import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import Skeleton from '@/views/records/partials/Skeleton.vue';
import Card from '@/views/records/partials/Card.vue';
import RecordsNavigation from '@/views/records/partials/Navigation.vue';

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const { setTitle } = usePageTitle();

const {
  categories,
  registers,
  tags,
  normalizeCategoryRegisterData,
  normalizeTagsData
} = useNormalizeData();

const isLoading = ref(false);
const archive = ref(null);
const records = ref([]);

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

    const [archiveData, categories, tags] = await Promise.all([
      getArchive(uuid.value),
      getCategoriesAndRegisters(uuid.value),
      getTags(uuid.value)
    ]);

    normalizeCategoryRegisterData(categories);
    normalizeTagsData(tags);

    archive.value = archiveData;
    setTitle(archive.value.name);

    const recordsData = await getRecords(uuid.value);
    records.value = recordsData;

  }
  catch (error) {
    console.error(error);
  }
  finally {
    isLoading.value = false;
  }
});

</script>


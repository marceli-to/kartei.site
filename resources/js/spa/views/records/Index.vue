<template>
  <div 
    v-if="!isLoading"
    class="flex flex-grow w-full">
    <div class="flex mt-106 min-h-full w-full">
      <ContentNavigation>
        <div class="flex flex-col gap-y-20">
          <InputGroup>
            <InputSearch
              v-model="searchQuery"
              placeholder="Suche"
              aria-label="Suche" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Sortierfolge" id="sort_order" />
            <InputSelect
              id="sort_order"
              v-model="sortOrder"
              :options="sortOrderOptions"
              variant="box"
            />
          </InputGroup>

          <InputGroup v-if="categories.length > 0">
            <InputLabel label="Kategorien" id="category" />
            <InputSelectButtons
              v-model="selectedCategories"
              :multiple="true"
              name="sections"
              :options="categories"
              classes="!text-black"
              wrapperClasses="flex flex-col gap-y-8" />
          </InputGroup>
          
          <InputGroup v-if="registers.length > 0">
            <InputLabel label="Register" id="register" />
            <InputSelectButtons
              v-model="selectedRegisters"
              :multiple="true"
              name="sections"
              :options="registers"
              classes="!text-black"
              wrapperClasses="flex flex-col gap-y-8" />
          </InputGroup>
        </div>
      </ContentNavigation>
      <ContentMain>
        <div 
          v-if="records.length > 0"
          class="flex flex-wrap gap-x-16 gap-y-32">

        </div>
      </ContentMain>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePageTitle } from '@/composables/usePageTitle';
import { getStructureCategories } from '@/services/api/archiveStructure';
import { getArchive } from '@/services/api/archive';

import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import InputSearch from '@/components/forms/Search.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputGroup from '@/components/forms/Group.vue';

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const { setTitle } = usePageTitle();

const isLoading = ref(false);
const searchQuery = ref('');
const sortOrder = ref('name');
const records = ref([]);

const selectedCategories = ref([]);
const selectedRegisters = ref([]);

const categories = ref([]);
const registers = ref([]);
const archive = ref(null);

const sortOrderOptions = [
  { value: 'name', label: 'Name' },
  { value: 'date', label: 'Datum' },
  { value: 'created_at', label: 'Erstellt' },
  { value: 'updated_at', label: 'Aktualisiert' },
];

onMounted(async () => {
  try {
    isLoading.value = true;

    const [structure, archiveData] = await Promise.all([
      getStructureCategories(uuid.value),
      getArchive(uuid.value)
    ]);

    normalizeStructure(structure);
    archive.value = archiveData;
    setTitle(archive.value.name);
  } 
  catch (error) {
    console.error(error);
  }
  finally {
    isLoading.value = false;
  }
});

const normalizeStructure = (structureData) => {
  categories.value = structureData.categories.map(cat => ({
    label: cat.title,
    value: cat.uuid
  }));

  registers.value = structureData.registers.map(reg => ({
    label: reg.title,
    value: reg.uuid
  }));
};
</script>

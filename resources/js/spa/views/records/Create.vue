<template>
  <div class="flex flex-grow w-full">
    <div class="flex mt-106 min-h-full w-full">
      <ContentNavigation>
        <RecordsNavigation
          v-model="filters"
          :uuid="uuid"
          :categories="categories"
          :registers="registers"
          :tags="tags"
          :disabled="true" />
      </ContentNavigation>

      <ContentMain class="!pb-0">
        <form 
          class="w-full min-w-[800px] h-full flex flex-col justify-between pb-24 relative before:absolute before:inset-y-0 before:left-1/4 before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-3/4 after:w-1 after:bg-graphite after:-z-1"
          @submit.prevent="handleSubmit"
          v-if="!isLoading">
          <div class="flex flex-col h-full justify-between">
            <div class="flex h-full gap-x-17 relative z-10">
              <div class="w-3/12">
                <div class="relative flex flex-col gap-20 -top-24">
                  <InputGroup>
                    <InputLabel label="Ordnung" />
                    <InputSelectButtons
                      v-model="form.structure"
                      :multiple="true"
                      name="structure"
                      :options="structure"
                      classes="!text-black"
                      wrapperClasses="flex flex-col gap-y-8" />
                  </InputGroup>
                  <InputGroup>
                    <InputLabel label="Tags" />
                    <InputSelectButtons
                      v-model="form.tags"
                      :multiple="true"
                      name="tags"
                      :options="tags"
                      classes="!text-black"
                      wrapperClasses="flex flex-col gap-y-8" />
                  </InputGroup>
                </div>
              </div>
              <div class="w-6/12">
                <div class="relative -top-24">
                  <InputGroup>
                    <InputLabel label="Bilder" />
                    <template v-if="template.image">
                      <ImageUpload
                        :maxSize="250 * 1024 * 1024"
                        :allowedTypes="['image/*']"
                        uploadUrl="/api/upload"
                        :multiple="true"
                        v-model="form.images"
                        :existingImages="form.images"
                        :error="errors.images"
                        classes="!aspect-[16/9]" />
                    </template>
                    <template v-else>
                      <ImageCard class="!aspect-[16/9] flex items-center justify-center">
                        <div class="flex flex-col items-center">
                          <IconImage variant="missing" />
                          <div class="text-sm mt-8">
                            Kein Bildfeld definiert
                          </div>
                        </div>
                      </ImageCard>
                    </template>
                  </InputGroup>
                </div>
              </div>
              <div class="w-3/12">
                <div class="relative -top-24">
                  <InputLabel label="Beschreibung" />
                  <InputStatic class="font-muoto-medium mb-8">
                    Nr. / {{ archive?.acronym }}_
                  </InputStatic>
                  <InputGroup 
                    class="flex flex-col gap-y-8"
                    v-if="template.fields.length > 0">
                    <InputText
                      v-for="(value, index) in template.fields"
                      :key="index"
                      v-model="form.fields[index]"
                      :id="'field-' + index"
                      :placeholder="value.placeholder"
                      :aria-label="'field-' + index" />
                  </InputGroup>
                </div>
              </div>
            </div>
            <ButtonGroup class="pb-8 relative z-10">
              <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
              <ButtonPrimary type="button" label="Abbrechen" @click="handleCancel" />
            </ButtonGroup>
          </div>
        </form>
      </ContentMain>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getArchive } from '@/services/api/archive';
import { getStructureCategories } from '@/services/api/archiveStructure';
import { getTags } from '@/services/api/tags';
import { getStructure } from '@/services/api/archiveStructure';
import { getTemplate } from '@/services/api/archiveTemplate';
import { useNormalizeData } from '@/views/records/composables/useNormalizeData';
import { usePageTitle } from '@/composables/usePageTitle';

import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import RecordsNavigation from '@/views/records/partials/Navigation.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import ImageUpload from '@/components/media/upload/Image.vue';
import ImageCard from '@/components/media/Card.vue';
import IconImage from '@/components/icons/Image.vue';

const router = useRouter();
const route = useRoute();
const uuid = ref(route.params.uuid || null);

const { setTitle } = usePageTitle();

const isLoading = ref(false);
const isSaving = ref(false);

const archive = ref(null);
const template = ref({
  image: null,
  fields: []
});

const {
  structure,
  categories,
  registers,
  tags,
  normalizeStructureData,
  normalizeCategoryData,
  normalizeTagsData
} = useNormalizeData();


const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: []
});

const form = ref({
  images: [],
  structure: [],
  tags: [],
  fields: []
});

const errors = ref({
  images: null,
});

const handleSubmit = () => { };

const handleCancel = () => {
  router.push({ name: 'archiveRecords', params: { uuid: uuid.value } });
};

onMounted(async () => {
  try {
    isLoading.value = true;

    const [archiveData, structure, categories, tags, templateData] = await Promise.all([
      getArchive(uuid.value),
      getStructure(uuid.value),
      getStructureCategories(uuid.value),
      getTags(uuid.value),
      getTemplate(uuid.value)
    ]);

    archive.value = archiveData;
    setTitle(archive.value.name);

    template.value = templateData.data;

    normalizeStructureData(structure.data);
    normalizeCategoryData(categories);
    normalizeTagsData(tags);
  } 
  catch (error) {
    console.error(error);
  }
  finally {
    isLoading.value = false;
  }
});

</script>

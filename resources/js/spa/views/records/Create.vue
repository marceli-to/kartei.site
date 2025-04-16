<template>
  <div class="flex flex-grow w-full relative">
    <Actions>
      <router-link 
        :to="{ name: 'archiveRecords', params: { uuid: uuid } }"
        v-if="uuid">
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
          :tags="tags"
          :disabled="true">
        </Sidebar>
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
                      v-model="form.category"
                      :multiple="false"
                      name="categoriesRegisters"
                      :options="categoriesRegisters"
                      classes="!text-black"
                      wrapperClasses="flex flex-col gap-y-8"
                      v-if="categoriesRegisters.length" />
                  </InputGroup>
                  <InputGroup>
                    <InputLabel label="Tags" />
                    <InputSelectButtons
                      v-model="form.tags"
                      :multiple="true"
                      name="tags"
                      :options="tags"
                      classes="!text-black"
                      wrapperClasses="flex flex-col gap-y-8"
                      v-if="tags.length > 0" />
                  </InputGroup>
                </div>
              </div>
              <div class="w-6/12">
                <div class="relative -top-24">
                  <InputGroup>
                    <InputLabel label="Bilder" />
                    <template v-if="settings.has_images">
                      <ImageUpload
                        :maxSize="250 * 1024 * 1024"
                        :allowedTypes="['image/*']"
                        uploadUrl="/api/upload"
                        :multiple="true"
                        v-model="form.media"
                        :existingImages="form.media"
                        :error="null"
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
                  <InputGroup 
                    class="flex flex-col gap-y-8"
                    v-if="settings.record_fields.length > 0">
                    <InputTextarea
                      v-for="(value, index) in settings.record_fields"
                      :key="index"
                      v-model="form.fields[index].content"
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
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePageTitle } from '@/composables/usePageTitle'
import { useNormalizeData } from '@/views/records/composables/useNormalizeData'
import { getArchive, getArchiveMeta } from '@/services/api/archive'
import { createRecord } from '@/services/api/record'

import ContentNavigation from '@/components/layout/ContentNavigation.vue'
import ContentMain from '@/components/layout/ContentMain.vue'
import Sidebar from '@/views/records/partials/Sidebar.vue'
import Actions from '@/views/records/partials/Actions.vue'
import ButtonGroup from '@/components/buttons/Group.vue'
import ButtonPrimary from '@/components/buttons/Primary.vue'
import InputGroup from '@/components/forms/Group.vue'
import InputSelectButtons from '@/components/forms/SelectButtons.vue'
import InputLabel from '@/components/forms/Label.vue'
import InputTextarea from '@/components/forms/Textarea.vue'
import ImageUpload from '@/components/media/upload/Image.vue'
import ImageCard from '@/components/media/Card.vue'
import IconImage from '@/components/icons/Image.vue'
import IconChevronLeft from '@/components/icons/ChevronLeft.vue'

const router = useRouter()
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
const isSaving = ref(false)

const archive = ref(null)
const settings = ref({
  has_images: null,
  record_fields: []
})

const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: []
})

const form = ref({
  media: [],
  category: '',
  tags: [],
  fields: []
})

const handleSubmit = async () => {
  isSaving.value = true
  const recordData = {
    archive_id: uuid.value,
    tags: form.value.tags,
    category: form.value.category,
    fields: form.value.fields,
    media: form.value.media
  }

  try {
    await createRecord(recordData)
    router.push({ name: 'archiveRecords', params: { uuid: uuid.value } })
  }
  catch (error) {
    console.error(error)
  }
  finally {
    isSaving.value = false
  }
}

const handleCancel = () => {
  router.push({ name: 'archiveRecords', params: { uuid: uuid.value } })
}

onMounted(async () => {
  try {
    isLoading.value = true;
    await loadData();
  } 
  catch (error) {
    console.error(error)
  } 
  finally {
    isLoading.value = false
  }
});

const loadData = async () => {
  const [archiveData, archiveMetaData] = await Promise.all([
    getArchive(uuid.value),
    getArchiveMeta(uuid.value),
  ])

  normalizeCategoryRegisterData({
    categories: archiveMetaData.categories,
    registers: archiveMetaData.registers
  });
  normalizeTagsData(archiveMetaData.tags);
  normalizeCategoryData(archiveMetaData.categories_registers);

  archive.value = archiveData;
  setTitle(archive.value.name);

  // Pre-populate field structure based on settings
  settings.value = archiveMetaData.settings;
  settings.value.record_fields.forEach(field => {
    form.value.fields.push({
      uuid: field.uuid,
      placeholder: field.placeholder,
      content: null
    })
  })
}
</script>

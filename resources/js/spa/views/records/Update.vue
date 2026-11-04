<template>
  <div class="flex flex-grow w-full relative" 
    v-if="!isLoading">
    <Actions :links="actionLinks">
      <router-link 
        :to="{ name: 'archiveRecords', params: { uuid: record.archive.uuid } }"
        v-if="record && record.archive && record.archive.uuid"
        aria-label="Zurück zur Kartei">
        <IconChevronLeft variant="small-bold" />
      </router-link>
    </Actions>
    <div class="flex mt-106 min-h-full w-full">
      <ContentNavigation>
        <Sidebar
          v-model="filters"
          :uuid="record.archive.uuid"
          :categories="categories"
          :registers="registers"
          :tags="tags"
          :disabled="true">
        </Sidebar>
      </ContentNavigation>

      <ContentMain class="!pb-0">
        <form 
          class="w-full min-w-[800px] h-full flex flex-col justify-between pb-24 relative before:absolute before:inset-y-0 before:left-1/4 before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-3/4 after:w-1 after:bg-graphite after:-z-1"
          @submit.prevent="handleSubmit">
          <div class="flex flex-col h-full justify-between">
            <div class="flex h-full gap-x-17 relative z-10">
              <div class="w-3/12">
                <div class="relative flex flex-col gap-20 -top-24">
                  <InputGroup>
                    <InputLabel label="Ordnung" :error="errors.category" />
                    <InputSelectButtons
                      legend="Ordnung"
                      v-model="form.category"
                      :multiple="false"
                      name="categoriesRegisters"
                      :options="categoriesRegisters"
                      classes="!text-black"
                      @update:modelValue="errors.category = null"
                      wrapperClasses="flex flex-col gap-y-8"
                      v-if="categoriesRegisters.length" />
                  </InputGroup>
                  <InputGroup>
                    <InputLabel label="Tags" />
                    <InputSelectButtons
                      legend="Tags"
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
                        :allowedTypes="['image/*']"
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
                    v-if="form.fields.length > 0">
                    <InputTextarea
                      v-for="(field, index) in form.fields"
                      :key="field.uuid"
                      v-model="field.content"
                      :id="'field-' + index"
                      :placeholder="field.placeholder"
                      :aria-label="field.placeholder" />
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
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePageTitle } from '@/composables/usePageTitle'
import { useNormalizeData } from '@/views/records/composables/useNormalizeData'
import { useFormErrors } from '@/composables/useFormErrors'
import { useDialogStore } from '@/stores/dialog'
import { getArchiveMeta } from '@/services/api/archive'
import { getRecord, updateRecord, deleteRecord } from '@/services/api/record'
import { useUserStore } from '@/stores/user'

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
import IconCross from '@/components/icons/Cross.vue'
import IconChevronLeft from '@/components/icons/ChevronLeft.vue'
import { notifyError } from '@/services/api/error'

const router = useRouter()
const route = useRoute()
const uuid = ref(route.params.uuid || null)

const dialogStore = useDialogStore()

const handleDelete = () => {
  dialogStore.confirmDelete({
    title: 'Karte löschen',
    message: 'Möchten Sie diese Karte wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
    action: () => deleteRecord(record.value.uuid),
    then: () => router.push({ name: 'archiveRecords', params: { uuid: record.value.archive.uuid } }),
    success: 'Karte erfolgreich gelöscht',
    failure: 'Fehler beim Löschen der Karte',
  })
}

const userStore = useUserStore()
const canDelete = computed(() => !!record.value && userStore.canArchive(record.value.archive.uuid, 'deleteRecord'))

const actionLinks = computed(() => canDelete.value ? [
  {
    type: 'button',
    label: 'Löschen',
    icon: IconCross,
    onClick: handleDelete,
  },
] : []);


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
const isSaving = ref(false)

const archive = ref(null)
const record = ref(null)
const settings = ref({
  has_images: null,
  record_fields: []
});

const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: []
});

const form = ref({
  media: [],
  category: '',
  tags: [],
  fields: []
});

const { errors, setErrors, clearErrors } = useFormErrors(['category'])

const handleSubmit = async () => {
  isSaving.value = true;
  clearErrors()
  const recordData = {
    archive_id: record.value.archive.uuid,
    tags: form.value.tags,
    category: form.value.category,
    fields: form.value.fields,
    media: form.value.media
  }

  try {
    await updateRecord(record.value.uuid, recordData)
    router.push({ name: 'archiveRecords', params: { uuid: record.value.archive.uuid } })
  } 
  catch (error) {
    setErrors(error)
    notifyError(error, error.response?.data?.message || 'Fehler beim Speichern der Karte.')
  } 
  finally {
    isSaving.value = false
  }
}

const handleCancel = () => {
  router.push({ name: 'archiveRecords', params: { uuid: record.value.archive.uuid } })
}

onMounted(async () => {
  try {
    const loaded = await loadRecordAndArchive(uuid.value);
    if (loaded) populateForm();
  } 
  catch (error) {
    notifyError(error, 'Die Karte konnte nicht geladen werden.');
  } 
  finally {
    isLoading.value = false;
  }
});

const loadRecordAndArchive = async (recordUuid) => {
  const recordData = await getRecord(recordUuid);

  // The route only knows the record uuid; check the archive ability now
  if (!userStore.canArchive(recordData.archive.uuid, 'updateRecord')) {
    router.replace({ name: '403' });
    return;
  }

  const archiveMetaData = await getArchiveMeta(recordData.archive.uuid);

  record.value = recordData;
  archive.value = recordData.archive;
  setTitle(archive.value.name);

  settings.value = archiveMetaData.settings;

  normalizeCategoryRegisterData({
    categories: archiveMetaData.categories,
    registers: archiveMetaData.registers
  });

  normalizeTagsData(archiveMetaData.tags);
  normalizeCategoryData(archiveMetaData.categories_registers);
  return true;
}

const populateForm = () => {
  const { category, tags, media, fields = [] } = record.value;
  const settingFields = settings.value?.record_fields || [];

  form.value = {
    category: category?.uuid || null,
    tags,
    media,
    fields: mergeSettingFields(fields, settingFields)
  };
}

// One field per setting, in settings order. Empty fields are not stored, so the
// record's own list can have gaps or a different order; match by uuid instead.
const mergeSettingFields = (existingFields, settingFields) => {
  const byUuid = new Map(existingFields.map(field => [field.uuid, field]));

  return settingFields.map(setting => ({
    uuid: setting.uuid,
    placeholder: setting.placeholder,
    content: byUuid.get(setting.uuid)?.content ?? null
  }));
}
</script>

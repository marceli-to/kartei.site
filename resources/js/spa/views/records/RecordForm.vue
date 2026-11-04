<template>
  <div class="flex flex-grow w-full relative">
    <Actions :links="actionLinks">
      <router-link 
        :to="{ name: 'archiveRecords', params: { uuid: archiveUuid } }"
        v-if="archiveUuid"
        aria-label="Zurück zur Kartei">
        <Icon name="chevron-left" variant="small-bold" />
      </router-link>
    </Actions>
    <div class="flex mt-106 min-h-full w-full">
      <ContentNavigation>
        <Sidebar
          v-if="archiveUuid"
          v-model="filters"
          :uuid="archiveUuid"
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
                        :error="null"
                        classes="!aspect-[16/9]" />
                    </template>
                    <template v-else>
                      <ImageCard class="!aspect-[16/9] flex items-center justify-center">
                        <div class="flex flex-col items-center">
                          <Icon name="image" variant="missing" class="text-graphite" />
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
              <AppButton type="submit" label="Speichern" :disabled="isSaving" />
              <AppButton type="button" label="Abbrechen" @click="handleCancel" />
            </ButtonGroup>
          </div>
        </form>
      </ContentMain>
    </div>
  </div>
</template>

<script setup>
// One view for creating and editing a record:
// - archiveRecordCreate: /archiv/kartei/karte/erstellen/:uuid (archive uuid)
// - archiveRecordUpdate: /archiv/kartei/karte/bearbeiten/:uuid (record uuid)
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePageTitle } from '@/composables/usePageTitle'
import { useArchiveMeta } from '@/views/records/composables/useArchiveMeta'
import { useFormErrors } from '@/composables/useFormErrors'
import { useDialogStore } from '@/stores/dialog'
import { useUserStore } from '@/stores/user'
import { getArchive } from '@/services/api/archive'
import { getRecord, createRecord, updateRecord, deleteRecord } from '@/services/api/record'
import { notifyError } from '@/services/api/error'

import ContentNavigation from '@/components/layout/ContentNavigation.vue'
import ContentMain from '@/components/layout/ContentMain.vue'
import Sidebar from '@/views/records/partials/Sidebar.vue'
import Actions from '@/views/records/partials/Actions.vue'
import ButtonGroup from '@/components/buttons/Group.vue'
import InputGroup from '@/components/forms/Group.vue'
import InputSelectButtons from '@/components/forms/SelectButtons.vue'
import InputLabel from '@/components/forms/Label.vue'
import InputTextarea from '@/components/forms/Textarea.vue'
import ImageUpload from '@/components/media/upload/Image.vue'
import ImageCard from '@/components/media/Card.vue'
import Icon from '@/components/icons/Icon.vue'
import AppButton from '@/components/buttons/Button.vue'

const router = useRouter()
const route = useRoute()
const dialogStore = useDialogStore()
const userStore = useUserStore()
const { setTitle } = usePageTitle()

const isEdit = route.name === 'archiveRecordUpdate'
const record = ref(null)
const archiveUuid = ref(isEdit ? null : route.params.uuid)

const {
  categories,
  categoriesRegisters,
  registers,
  tags,
  settings,
  loadArchiveMeta
} = useArchiveMeta()

const isLoading = ref(true)
const isSaving = ref(false)

// The sidebar is shown for orientation only (disabled)
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

const { errors, setErrors, clearErrors } = useFormErrors(['category'])

const backToList = () => router.push({ name: 'archiveRecords', params: { uuid: archiveUuid.value } })

const canDelete = computed(() => isEdit && !!record.value && userStore.canArchive(archiveUuid.value, 'deleteRecord'))

const actionLinks = computed(() => canDelete.value ? [
  {
    type: 'button',
    label: 'Löschen',
    icon: 'cross',
    onClick: () => dialogStore.confirmDelete({
      title: 'Karte löschen',
      message: 'Möchten Sie diese Karte wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
      action: () => deleteRecord(record.value.uuid),
      then: backToList,
      success: 'Karte erfolgreich gelöscht',
      failure: 'Fehler beim Löschen der Karte',
    }),
  },
] : [])

const handleSubmit = async () => {
  isSaving.value = true
  clearErrors()
  const recordData = {
    archive_id: archiveUuid.value,
    tags: form.value.tags,
    category: form.value.category,
    fields: form.value.fields,
    media: form.value.media
  }

  try {
    if (isEdit) {
      await updateRecord(record.value.uuid, recordData)
    }
    else {
      await createRecord(recordData)
    }
    backToList()
  }
  catch (error) {
    setErrors(error)
    notifyError(error, error.response?.data?.message || 'Fehler beim Speichern der Karte.')
  }
  finally {
    isSaving.value = false
  }
}

const handleCancel = backToList

onMounted(async () => {
  try {
    if (isEdit) {
      await loadRecord(route.params.uuid)
    }
    else {
      const [archive] = await Promise.all([getArchive(archiveUuid.value), loadArchiveMeta(archiveUuid.value)])
      setTitle(archive.name)
      form.value.fields = mergeSettingFields([], settings.value.record_fields)
    }
  }
  catch (error) {
    notifyError(error, isEdit ? 'Die Karte konnte nicht geladen werden.' : 'Die Kartei konnte nicht geladen werden.')
  }
  finally {
    isLoading.value = false
  }
})

const loadRecord = async (recordUuid) => {
  const recordData = await getRecord(recordUuid)

  // The route only knows the record uuid; check the archive ability now
  if (!userStore.canArchive(recordData.archive.uuid, 'updateRecord')) {
    router.replace({ name: '403' })
    return
  }

  await loadArchiveMeta(recordData.archive.uuid)
  record.value = recordData
  archiveUuid.value = recordData.archive.uuid
  setTitle(recordData.archive.name)

  const { category, tags, media, fields = [] } = recordData
  form.value = {
    category: category?.uuid || null,
    tags,
    media,
    fields: mergeSettingFields(fields, settings.value.record_fields)
  }
}

// One field per setting, in settings order. Empty fields are not stored, so the
// record's own list can have gaps or a different order; match by uuid instead.
const mergeSettingFields = (existingFields, settingFields = []) => {
  const byUuid = new Map(existingFields.map(field => [field.uuid, field]))

  return settingFields.map(setting => ({
    uuid: setting.uuid,
    placeholder: setting.placeholder,
    content: byUuid.get(setting.uuid)?.content ?? null
  }))
}
</script>

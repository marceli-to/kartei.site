<template>
  <div class="relative flex w-full grow">
    <Actions :links="actionLinks">
      <router-link
        v-if="archiveUuid"
        :to="{ name: 'archiveRecords', params: { uuid: archiveUuid } }"
        aria-label="Zurück zur Kartei">
        <Icon
          name="chevron-left"
          variant="small-bold" />
      </router-link>
    </Actions>
    <div class="mt-106 flex min-h-full w-full">
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

      <ContentMain class="pb-0!">
        <form
          v-if="!isLoading"
          class="relative flex h-full w-full min-w-[800px] flex-col justify-between pb-24 before:absolute before:inset-y-0 before:left-1/4 before:w-1 before:bg-graphite after:absolute after:inset-y-0 after:left-3/4 after:w-1 after:bg-graphite"
          @submit.prevent="handleSubmit">
          <div class="flex h-full flex-col justify-between">
            <div class="relative z-10 flex h-full gap-x-17">
              <div class="w-3/12">
                <div class="relative -top-24 flex flex-col gap-20">
                  <InputGroup>
                    <InputLabel
                      label="Ordnung"
                      :error="errors.category" />
                    <InputSelectButtons
                      v-if="categoriesRegisters.length"
                      v-model="form.category"
                      legend="Ordnung"
                      :multiple="false"
                      name="categoriesRegisters"
                      :options="categoriesRegisters"
                      classes="text-black!"
                      wrapper-classes="flex flex-col gap-y-8"
                      @update:model-value="errors.category = null" />
                  </InputGroup>
                  <InputGroup>
                    <InputLabel label="Tags" />
                    <InputSelectButtons
                      v-if="tags.length > 0"
                      v-model="form.tags"
                      legend="Tags"
                      :multiple="true"
                      name="tags"
                      :options="tags"
                      classes="text-black!"
                      wrapper-classes="flex flex-col gap-y-8" />
                  </InputGroup>
                </div>
              </div>
              <div class="w-6/12">
                <div class="relative -top-24">
                  <InputGroup>
                    <InputLabel label="Bilder" />
                    <template v-if="settings.has_images">
                      <ImageUpload
                        v-model="form.media"
                        :allowed-types="['image/*']"
                        :multiple="true"
                        :error="null"
                        classes="aspect-video!" />
                    </template>
                    <template v-else>
                      <ImageCard class="flex aspect-video! items-center justify-center">
                        <div class="flex flex-col items-center">
                          <Icon
                            name="image"
                            variant="missing"
                            class="text-graphite" />
                          <div class="mt-8 text-sm">Kein Bildfeld definiert</div>
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
                    v-if="form.fields.length > 0"
                    class="flex flex-col gap-y-8">
                    <div
                      v-for="(field, index) in form.fields"
                      :key="field.uuid">
                      <InputTextarea
                        :id="'field-' + index"
                        v-model="field.content"
                        :max-length="FIELD_MAX_LENGTH"
                        :placeholder="field.placeholder"
                        :aria-label="field.placeholder"
                        :aria-describedby="'field-' + index + '-count'" />
                      <div
                        :id="'field-' + index + '-count'"
                        class="mt-2 text-right text-xs text-graphite">
                        {{ field.content?.length ?? 0 }} / {{ FIELD_MAX_LENGTH }}
                      </div>
                    </div>
                  </InputGroup>
                </div>
              </div>
            </div>
            <ButtonGroup class="relative z-10 pb-8">
              <AppButton
                type="submit"
                label="Speichern"
                :disabled="isSaving" />
              <AppButton
                type="button"
                label="Abbrechen"
                @click="handleCancel" />
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
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { usePageTitle } from '@/composables/usePageTitle';
import { useArchiveMeta } from '@/views/records/composables/useArchiveMeta';
import { useFormErrors } from '@/composables/useFormErrors';
import { useDialogStore } from '@/stores/dialog';
import { useUserStore } from '@/stores/user';
import { getArchive } from '@/services/api/archive';
import { getRecord, createRecord, updateRecord, deleteRecord } from '@/services/api/record';
import { notifyError } from '@/services/api/error';

import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import Sidebar from '@/views/records/partials/Sidebar.vue';
import Actions from '@/views/records/partials/Actions.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputTextarea from '@/components/forms/Textarea.vue';
import ImageUpload from '@/components/media/upload/Image.vue';
import ImageCard from '@/components/media/Card.vue';
import Icon from '@/components/icons/Icon.vue';
import AppButton from '@/components/buttons/Button.vue';

const router = useRouter();
const route = useRoute();
const dialogStore = useDialogStore();
const userStore = useUserStore();
const { setTitle } = usePageTitle();

const isEdit = route.name === 'archiveRecordUpdate';
// Same limit as the server (record_fields.content is a string column)
const FIELD_MAX_LENGTH = 255;

const record = ref(null);
const archiveUuid = ref(isEdit ? null : route.params.uuid);

const { categories, categoriesRegisters, registers, tags, settings, loadArchiveMeta } = useArchiveMeta();

const isLoading = ref(true);
const isSaving = ref(false);

// The sidebar is shown for orientation only (disabled)
const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: [],
});

const form = ref({
  media: [],
  category: '',
  tags: [],
  fields: [],
});

const { errors, setErrors, clearErrors } = useFormErrors(['category']);

const backToList = () => router.push({ name: 'archiveRecords', params: { uuid: archiveUuid.value } });

const canDelete = computed(() => isEdit && !!record.value && userStore.canArchive(archiveUuid.value, 'deleteRecord'));

const actionLinks = computed(() =>
  canDelete.value
    ? [
        {
          type: 'button',
          label: 'Löschen',
          icon: 'cross',
          onClick: () =>
            dialogStore.confirmDelete({
              title: 'Karte löschen',
              message: 'Möchten Sie diese Karte wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
              action: () => deleteRecord(record.value.uuid),
              then: backToList,
              success: 'Karte erfolgreich gelöscht',
              failure: 'Fehler beim Löschen der Karte',
            }),
        },
      ]
    : [],
);

const handleSubmit = async () => {
  isSaving.value = true;
  clearErrors();
  const recordData = {
    archive_id: archiveUuid.value,
    tags: form.value.tags,
    category: form.value.category,
    fields: form.value.fields,
    media: form.value.media,
  };

  try {
    if (isEdit) {
      await updateRecord(record.value.uuid, recordData);
    } else {
      await createRecord(recordData);
    }
    backToList();
  } catch (error) {
    setErrors(error);
    notifyError(error, error.response?.data?.message || 'Fehler beim Speichern der Karte.');
  } finally {
    isSaving.value = false;
  }
};

const handleCancel = backToList;

onMounted(async () => {
  try {
    if (isEdit) {
      await loadRecord(route.params.uuid);
    } else {
      const [archive] = await Promise.all([getArchive(archiveUuid.value), loadArchiveMeta(archiveUuid.value)]);
      setTitle(archive.name);
      form.value.fields = mergeSettingFields([], settings.value.record_fields);
    }
  } catch (error) {
    notifyError(error, isEdit ? 'Die Karte konnte nicht geladen werden.' : 'Die Kartei konnte nicht geladen werden.');
  } finally {
    isLoading.value = false;
  }
});

const loadRecord = async (recordUuid) => {
  const recordData = await getRecord(recordUuid);

  // The route only knows the record uuid; check the archive ability now
  if (!userStore.canArchive(recordData.archive.uuid, 'updateRecord')) {
    router.replace({ name: '403' });
    return;
  }

  await loadArchiveMeta(recordData.archive.uuid);
  record.value = recordData;
  archiveUuid.value = recordData.archive.uuid;
  setTitle(recordData.archive.name);

  const { category, tags, media, fields = [] } = recordData;
  form.value = {
    category: category?.uuid || null,
    tags,
    media,
    fields: mergeSettingFields(fields, settings.value.record_fields),
  };
};

// One field per setting, in settings order. Empty fields are not stored, so the
// record's own list can have gaps or a different order; match by uuid instead.
const mergeSettingFields = (existingFields, settingFields = []) => {
  const byUuid = new Map(existingFields.map((field) => [field.uuid, field]));

  return settingFields.map((setting) => ({
    uuid: setting.uuid,
    placeholder: setting.placeholder,
    content: byUuid.get(setting.uuid)?.content ?? null,
  }));
};
</script>

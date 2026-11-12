<template>
  <div class="relative flex w-full grow">
    <Actions :links="actionLinks">
      <!-- Back to the archive, like "Meine Karteien" on the list; a long name is shortened with […] -->
      <router-link
        v-if="archiveUuid"
        :to="{ name: 'archiveRecords', params: { uuid: archiveUuid } }"
        class="flex min-w-0 items-center gap-x-12 pr-16"
        :aria-label="`Zurück zu ${archiveName}`">
        <Icon
          name="chevron-left"
          variant="small-bold"
          class="shrink-0" />
        <FitText
          :text="archiveName"
          class="font-muoto-medium leading-none" />
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
        <!--
          lg and up: quarter-width columns Ordnung | Bilder | Beschreibung, then whitespace, with a line
          after each column; without an image field (card template "Ohne Bildfeld") Ordnung | Beschreibung.
          Narrower: one column, Bilder, Beschreibung, then Ordnung/Tags. Labels sit above the
          content line only in the column layout (lg:-top-24).
        -->
        <form
          v-if="!isLoading"
          class="relative flex h-full w-full flex-col justify-between gap-y-32 pb-24"
          @submit.prevent="handleSubmit">
          <div
            v-for="line in settings.has_images ? ['left-1/4', 'left-1/2', 'left-3/4'] : ['left-1/4', 'left-1/2']"
            :key="line"
            class="absolute inset-y-0 hidden w-1 bg-graphite lg:block"
            :class="line"
            aria-hidden="true"></div>
          <div class="relative z-10 grid grid-cols-1 gap-y-32 lg:h-full lg:grid-cols-4 lg:gap-y-0">
            <div class="order-3 lg:order-none lg:pr-8">
              <div class="flex flex-col gap-20 lg:relative lg:-top-24">
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
                    classes="text-black! dark:text-white!"
                    wrapper-classes="flex flex-col gap-y-8"
                    @update:model-value="errors.category = null" />
                  <SetupHint
                    v-else-if="isInfoOpen"
                    :archive-uuid="archiveUuid"
                    section="structure"
                    label="Ordnung"
                    :can-edit="canEditSetting('manageStructure')">
                    Organisieren Sie Ihre Kartei mittels Kategorien und Registern.
                  </SetupHint>
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
                    classes="text-black! dark:text-white!"
                    wrapper-classes="flex flex-col gap-y-8" />
                  <SetupHint
                    v-else-if="isInfoOpen"
                    :archive-uuid="archiveUuid"
                    section="tags"
                    label="Tags"
                    :can-edit="canEditSetting('manageTags')">
                    Versehen Sie Ihre Karten mit Tags (Schlagwörtern).
                  </SetupHint>
                </InputGroup>
              </div>
            </div>
            <div
              v-if="settings.has_images"
              class="order-1 lg:order-none lg:pr-8 lg:pl-9">
              <div class="lg:relative lg:-top-24">
                <InputGroup>
                  <InputLabel label="Bilder" />
                  <ImageUpload
                    v-model="form.media"
                    :allowed-types="['image/*']"
                    :multiple="true"
                    :error="null" />
                </InputGroup>
              </div>
            </div>
            <div class="order-2 lg:order-none lg:pr-8 lg:pl-9">
              <div class="flex flex-col gap-20 lg:relative lg:-top-24">
                <!-- As in the card template: the number is generated, so it is only shown -->
                <InputGroup>
                  <InputLabel label="Nr. / ID" />
                  <div class="flex min-h-default items-center justify-between gap-x-8 border-y border-graphite pr-8">
                    <span :class="{ 'font-muoto-medium': record }">
                      {{ record?.display_number || 'Nr. / ID (wird automatisch generiert)' }}
                    </span>
                    <Favorite
                      v-if="record"
                      :uuid="record.uuid" />
                  </div>
                </InputGroup>
                <InputGroup>
                  <InputLabel label="Beschreibung" />
                  <!-- Without text fields the hint takes the place of the fields, as in the design -->
                  <SetupHint
                    v-if="isInfoOpen && !form.fields.length"
                    :archive-uuid="archiveUuid"
                    section="card"
                    label="Kartenvorlage"
                    :can-edit="canEditSetting('manageTemplate')">
                    Definieren Sie in Ihrer Kartenvorlage Textfelder, um zugehörige Informationen einfach erfassen zu
                    können.
                  </SetupHint>
                  <div
                    v-else
                    class="flex flex-col gap-y-8">
                    <!-- The counter sits inside the field; the text keeps clear of it (pr-64) -->
                    <div
                      v-for="(field, index) in form.fields"
                      :key="field.uuid"
                      class="relative">
                      <InputTextarea
                        :id="'field-' + index"
                        v-model="field.content"
                        :max-length="FIELD_MAX_LENGTH"
                        :placeholder="field.placeholder"
                        :aria-label="field.placeholder"
                        :aria-describedby="'field-' + index + '-count'"
                        classes="resize-none! pr-64" />
                      <div
                        :id="'field-' + index + '-count'"
                        class="pointer-events-none absolute top-0 right-0 flex min-h-default items-center pr-8 text-xs text-graphite">
                        {{ field.content?.length ?? 0 }}&thinsp;/&thinsp;{{ FIELD_MAX_LENGTH }}
                      </div>
                    </div>
                  </div>
                </InputGroup>
              </div>
            </div>
          </div>
          <!-- The buttons end with the last column (before the whitespace) -->
          <ButtonGroup
            class="relative z-10 pb-8"
            :class="settings.has_images ? 'lg:w-[calc(75%-8px)]' : 'lg:w-[calc(50%-8px)]'">
            <AppButton
              type="submit"
              label="Speichern"
              :disabled="isSaving" />
            <AppButton
              type="button"
              label="Abbrechen"
              @click="handleCancel" />
          </ButtonGroup>
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
import FitText from '@/components/FitText.vue';
import Favorite from '@/views/records/partials/Favorite.vue';
import ImageUpload from '@/components/media/upload/Image.vue';
import Icon from '@/components/icons/Icon.vue';
import AppButton from '@/components/buttons/Button.vue';
import DeleteDialog from '@/views/records/partials/DeleteDialog.vue';
import SetupHint from '@/views/records/partials/SetupHint.vue';
import { useInfoBox } from '@/composables/useInfoBox';

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
const archiveName = ref('');

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

// Hints for what the archive is still missing (no categories, tags or text fields);
// they open by itself and can be hidden with the lightbulb
const isMissingSetup = computed(
  () => !isLoading.value && (!categoriesRegisters.value.length || !tags.value.length || !form.value.fields.length),
);
const { isOpen: isInfoOpen } = useInfoBox({ active: isMissingSetup, auto: true });

const canEditSetting = (ability) => !!archiveUuid.value && userStore.canArchive(archiveUuid.value, ability);

const backToList = () => router.push({ name: 'archiveRecords', params: { uuid: archiveUuid.value } });

const canDelete = computed(() => isEdit && !!record.value && userStore.canArchive(archiveUuid.value, 'deleteRecord'));

const actionLinks = computed(() =>
  canDelete.value
    ? [
        {
          type: 'button',
          label: 'Löschen',
          icon: 'cross',
          // Question with a preview of the saved record (Stackr #150)
          onClick: () =>
            dialogStore.show({
              component: DeleteDialog,
              props: {
                record: record.value,
                action: () => deleteRecord(record.value.uuid),
                then: backToList,
              },
              size: 'full',
              hideClose: true,
              hideDefaultActions: true,
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
      archiveName.value = archive.name;
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
  archiveName.value = recordData.archive.name;
  // The header shows the card's Nr. / ID (Stackr #154)
  setTitle(recordData.display_number);

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

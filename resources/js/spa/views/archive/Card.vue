<template>
  <Slide :pull="true">
    <form
      v-if="!isLoading"
      class="relative flex h-full w-full flex-col justify-between"
      @submit.prevent="handleSubmit">
      <!-- Content -->
      <div class="flex flex-col gap-y-20">
        <div>
          <InputLabel label="Bildfeld" />
          <ImageCard class="mb-8 flex aspect-video! items-center justify-center">
            <Icon
              v-if="form.has_images"
              name="image"
              class="text-graphite" />
            <Icon
              v-else
              name="image"
              variant="missing"
              class="text-graphite" />
          </ImageCard>
          <InputGroup>
            <InputSelectButtons
              v-model="form.has_images"
              legend="Bildfeld"
              name="image"
              wrapper-classes="flex flex-col gap-y-8"
              :options="image_options" />
          </InputGroup>
        </div>

        <InputGroup>
          <InputLabel label="Nr. / ID" />
          <InputStatic> Nr. / ID (wird automatisch generiert) </InputStatic>
        </InputGroup>

        <div class="mt-16">
          <Action
            type="button"
            label="Textfeld"
            :icon="{ name: 'Plus', variant: 'small', position: 'center' }"
            @click="add" />
        </div>

        <div>
          <InputLabel label="Textfeld" />
          <draggable
            v-model="form.record_fields"
            :item-key="rowKey"
            class="flex flex-col gap-y-8"
            handle=".drag-handle"
            v-bind="dragOptions"
            @end="reorder">
            <template #item="{ element, index }">
              <InputGroup class="relative">
                <InputText
                  :ref="(el) => (inputRefs[index] = el)"
                  v-model="element.placeholder"
                  :placeholder="`Textfeld ${index + 1}`"
                  classes="pr-56"
                  aria-label="Textfeld" />

                <button
                  type="button"
                  class="absolute top-1/2 right-40 -translate-y-1/2"
                  :aria-label="`Textfeld ${index + 1} entfernen`"
                  @click="remove(index)">
                  <Icon
                    name="cross"
                    variant="small" />
                </button>
                <DragHandle />
              </InputGroup>
            </template>
          </draggable>
        </div>
      </div>

      <!-- Buttons -->
      <ButtonGroup>
        <AppButton
          type="submit"
          label="Speichern"
          :disabled="isSaving" />
      </ButtonGroup>
    </form>
  </Slide>
</template>
<script setup>
import { ref, onMounted, nextTick, toRaw } from 'vue';
import draggable from 'vuedraggable';
import { dragOptions } from '@/utils/dragOptions';
import { useRoute } from 'vue-router';
import { useToastStore } from '@/stores/toast';
import { getArchiveSettings, storeArchiveSettings, deleteArchiveSettingsField } from '@/services/api/archive';

import Slide from '@/components/slider/Slide.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputGroup from '@/components/forms/Group.vue';
import ImageCard from '@/components/media/Card.vue';
import Action from '@/components/buttons/Action.vue';
import { notifyError } from '@/services/api/error';
import Icon from '@/components/icons/Icon.vue';
import DragHandle from '@/components/forms/DragHandle.vue';
import AppButton from '@/components/buttons/Button.vue';

const toast = useToastStore();

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const isSaving = ref(false);
const isLoading = ref(!!uuid.value);

const inputRefs = ref([]);

const image_options = [
  { value: true, label: 'Bildfeld' },
  { value: false, label: 'Ohne Bildfeld' },
];

defineProps({
  isActive: {
    type: Boolean,
    default: false,
  },
});

// A fresh object each time: a shared one would keep whatever was typed into it
const emptyField = (order = 0) => ({
  uuid: '',
  placeholder: '',
  order,
});

// Stable drag keys per row object; new fields have no uuid yet
const rowKeys = new WeakMap();
let nextRowKey = 1;
const rowKey = (row) => {
  const raw = toRaw(row);
  if (!rowKeys.has(raw)) rowKeys.set(raw, nextRowKey++);
  return rowKeys.get(raw);
};

const form = ref({
  has_images: true,
  record_fields: [emptyField()],
});

onMounted(async () => {
  if (uuid.value) {
    try {
      const response = await getArchiveSettings(uuid.value);
      form.value.has_images = response.has_images;

      if (!response.record_fields || response.record_fields.length === 0) {
        form.value.record_fields = [emptyField()];
      } else {
        form.value.record_fields = response.record_fields;
      }
    } catch (error) {
      notifyError(error, 'Die Kartenvorlage konnte nicht geladen werden.');
    } finally {
      isLoading.value = false;
    }
  }
});

// Methods
const handleSubmit = async () => {
  isSaving.value = true;
  try {
    const validFields = form.value.record_fields.filter((field) => field.placeholder.trim() !== '');
    const payload = {
      has_images: form.value.has_images,
      record_fields: validFields,
    };

    const response = await storeArchiveSettings(uuid.value, payload);

    if (response?.record_fields?.length > 0) {
      form.value.record_fields = response.record_fields;
    } else {
      form.value.record_fields = [emptyField()];
    }

    toast.show('Kartenvorlage erfolgreich gespeichert.', 'success');
  } catch (error) {
    notifyError(error, 'Fehler beim Speichern der Kartenvorlage.');
  } finally {
    isSaving.value = false;
  }
};

const add = async () => {
  form.value.record_fields.push(emptyField(form.value.record_fields.length));
  await nextTick();
  // InputText's root element is the <input> itself
  const lastIndex = form.value.record_fields.length - 1;
  inputRefs.value[lastIndex]?.$el?.focus();
};

const remove = async (index) => {
  const fieldUuid = form.value.record_fields[index].uuid;
  if (fieldUuid) {
    try {
      await deleteArchiveSettingsField(fieldUuid, uuid.value);
    } catch (error) {
      notifyError(error, 'Fehler beim Löschen des Textfelds.');
      return;
    }
  }
  form.value.record_fields.splice(index, 1);
  if (form.value.record_fields.length === 0) {
    form.value.record_fields.push(emptyField());
  }
};

const reorder = () => {
  form.value.record_fields.forEach((field, index) => {
    field.order = index;
  });
};
</script>

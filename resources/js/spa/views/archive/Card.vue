<template>
  <Slide :pull="true">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between relative"
      v-if="!isLoading">

      <!-- Content -->
      <div class="flex flex-col gap-y-20">

        <div>
          <InputLabel label="Bildfeld" />
          <ImageCard class="flex items-center justify-center mb-8 !aspect-[16/9]">
            <IconImage v-if="form.has_images" />
            <IconImage variant="missing" v-else />
          </ImageCard>
          <InputGroup>
            <InputSelectButtons
              v-model="form.has_images"
              name="image"
              wrapperClasses="flex flex-col gap-y-8"
              :options="image_options" />
          </InputGroup>
        </div>

        <InputGroup>
          <InputLabel label="Nr. / ID" />
          <InputStatic>
            Nr. / ID (wird automatisch generiert)
          </InputStatic>
        </InputGroup>

        <div class="mt-16">
          <Action 
            type="button"
            label="Textfeld" 
            :icon="{ name: 'Plus', variant: 'small', position: 'center' }"
            @click="add" />
        </div>

        <div>
          <draggable 
            v-model="form.record_fields" 
            item-key="uuid" 
            class="flex flex-col gap-y-8" 
            handle=".drag-handle"
            :animation="200"
            @end="reorder">
            <template #item="{ element, index }">
              <InputGroup class="relative flex justify-between items-center">
                <!-- 
                  <span class="cursor-grab size-16 drag-handle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-16 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16M4 14h16" />
                    </svg>
                  </span>
                -->
                <InputText
                  v-model="element.placeholder"
                  :placeholder="`Textfeld ${index + 1}`"
                  aria-label="Textfeld"
                  :ref="el => inputRefs[index] = el" />

                <button 
                  type="button" 
                  class="absolute right-8 top-12"
                  @click="remove(index)">
                  <IconCross variant="small" />
                </button>
              </InputGroup>
            </template>
          </draggable>
        </div>

      </div>

      <!-- Buttons -->
      <ButtonGroup>
        <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      </ButtonGroup>
    </form>
  </Slide>
</template>
<script setup>
import { ref, onMounted, nextTick } from 'vue';
import draggable from 'vuedraggable';
import { useRoute } from 'vue-router';
import { useToastStore } from '@/components/toast/stores/toast';
import { getArchiveSettings, storeArchiveSettings, deleteArchiveSettingsField } from '@/services/api/archive';

import Slide from '@/components/slider/Slide.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputGroup from '@/components/forms/Group.vue';
import ImageCard from '@/components/media/Card.vue';
import IconImage from '@/components/icons/Image.vue';
import IconCross from '@/components/icons/Cross.vue';
import Action from '@/components/buttons/Action.vue';

const toast = useToastStore();

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const isSaving = ref(false);
const isLoading = ref(false);

const inputRefs = ref([]);

const image_options = [
  { value: true, label: 'Bildfeld' },
  { value: false, label: 'Ohne Bildfeld' }
];

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const emptyField = {
  uuid: '',
  placeholder: '',
  order: 0
};

const form = ref({
  has_images: true,
  record_fields: [emptyField]
});

onMounted(async () => {
  if (uuid.value) {
    isLoading.value = true;
    try {
      const response = await getArchiveSettings(uuid.value);
      form.value.has_images = response.has_images;

      if (!response.record_fields || response.record_fields.length === 0) {
        form.value.record_fields = [emptyField];
      }
      else {
        form.value.record_fields = response.record_fields;
      }
    } 
    catch (error) {
      console.log(error);
    }
    finally {
      isLoading.value = false;
    }
  }
});

// Methods
const handleSubmit = async () => {
  isSaving.value = true;
  try {
    isSaving.value = true;
    const validFields = form.value.record_fields.filter(field => field.placeholder.trim() !== '');
    const payload = {
      has_images: form.value.has_images,
      record_fields: validFields
    };

    const response = await storeArchiveSettings(uuid.value, payload);

    if (response?.record_fields.length > 0) {
      form.value.record_fields = response.record_fields;
    }
    else {
      form.value.record_fields = [emptyField];
    }

    toast.show('Kartenvorlage erfolgreich gespeichert.', 'success');
  }
  catch (error) {
    toast.show('Fehler beim Speichern der Kartenvorlage.', 'error');
  } 
  finally {
    isSaving.value = false;
  }
};

const add = async () => {
  form.value.record_fields.push({
    uuid: '',
    placeholder: '',
    order: form.value.record_fields.length
  });
  await nextTick();
  const lastIndex = form.value.record_fields.length - 1;
  inputRefs.value[lastIndex]?.$el?.querySelector('input')?.focus();
};

const remove = async (index) => {
  const fieldUuid = form.value.record_fields[index].uuid;
  if (fieldUuid) {
    try {
      await deleteArchiveSettingsField(fieldUuid, uuid.value);
    } 
    catch (error) {
      toast.show('Fehler beim Löschen des Textfelds.', 'error');
    }
  }
  form.value.record_fields.splice(index, 1);
  if (form.value.record_fields.length === 0) {
    form.value.record_fields.push(emptyField);
  }
};

const reorder = () => {
  form.value.record_fields.forEach((field, index) => {
    field.order = index;
  });
};
</script>

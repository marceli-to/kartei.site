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
          <ImageCard class="flex items-center justify-center mb-8 aspect-[16/9]">
            <IconImage v-if="form.image" />
            <IconImage variant="missing" v-else />
          </ImageCard>
          <InputGroup>
            <InputSelectButtons
              v-model="form.image"
              name="image"
              wrapperClasses="flex flex-col gap-y-8"
              :options="image_options" />
          </InputGroup>
        </div>

        <InputGroup>
          <InputLabel label="Nr. / ID" />
          <InputStatic class="font-muoto-medium">
            Nr. / ID (wird automatisch generiert)
          </InputStatic>
        </InputGroup>

        <div>
          <InputLabel label="Textfelder" />
          <div class="flex flex-col gap-y-8">
            <InputGroup
              class="relative"
              v-for="(field, index) in form.fields" :key="index">
              <InputText
                v-model="form.fields[index].placeholder"
                :placeholder="`Textfeld ${index + 1}`"
                aria-label="Textfeld"
                :ref="el => inputRefs[index] = el" />
                <button 
                  type="button" 
                  class="absolute right-8 top-1/2 -translate-y-1/2"
                  @click="remove(index)">
                  <IconCross variant="small" />
                </button>
            </InputGroup>
          </div>
        </div>
        <div class="mt-20">
          <Action 
            type="button"
            label="Textfeld" 
            :icon="{ name: 'Plus', variant: 'small', position: 'center' }"
            @click="add" />
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
import { useRoute } from 'vue-router';
import { useToastStore } from '@/components/toast/stores/toast';
import { getTemplate, storeTemplate, deleteTemplateField } from '@/services/api/archiveTemplate';
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
  { value: 1, label: 'Bildfeld' },
  { value: 0, label: 'Ohne Bildfeld' }
];

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const form = ref({
  image: 1,
  fields: [{
    uuid: null,
    placeholder: ''
  }]
});

onMounted(async () => {
  if (uuid.value) {
    isLoading.value = true;
    try {
      const response = await getTemplate(uuid.value);
      form.value.image = response.data.image;
      form.value.fields = response.data.fields;
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
    const validFields = form.value.fields.filter(field => field.placeholder.trim() !== '');
    const payload = {
      image: form.value.image,
      fields: validFields
    };

    const response = await storeTemplate(uuid.value, payload);

    if (response?.data?.fields) {
      form.value.fields = response.data.fields;
    }

    toast.show('Kartenvorlage erfolgreich gespeichert.', 'success');
  }
  catch (error) {
    toast.show('Fehler beim Speichern der Kartenvorlage.', 'error');
  } 
  finally {
    isSaving.value = false;
  }
}

const add = async () => {
  form.value.fields.push({
    uuid: null,
    placeholder: ''
  });
  await nextTick();
  const lastIndex = form.value.fields.length - 1;
  inputRefs.value[lastIndex]?.$el?.querySelector('input')?.focus();
}

const remove = async (index) => {
  const fieldUuid = form.value.fields[index].uuid;
  if (fieldUuid) {
    try {
      await deleteTemplateField(fieldUuid);
    } 
    catch (error) {
      toast.show('Fehler beim Löschen des Textfelds.', 'error');
    }
  }
  form.value.fields.splice(index, 1);
  if (form.value.fields.length === 0) {
    form.value.fields.push({ uuid: null, placeholder: '' });
  }
};
</script>

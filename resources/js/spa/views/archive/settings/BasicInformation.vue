<template>
  <Slide :pull="true">
    
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Form elements -->
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Titelbild" id="image" required />
          <ImageUpload
            :maxSize="250 * 1024 * 1024"
            :allowedTypes="['image/*']"
            uploadUrl="/api/upload"
            :multiple="false"
             v-model="form.image"
             :existingImages="form.image"
             :error="errors.image"
          />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Name" id="name" required />
          <InputText
            v-model="form.name"
            id="name"
            :error="errors.name"
            @update:error="errors.name = $event"
            :placeholder="errors.name ? errors.name : 'Name'"
            aria-label="Name" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Kürzel (max. 5 Zeichen)" id="acronym" required />
          <InputText
            v-model="form.acronym"
            id="acronym"
            :error="errors.acronym"
            @update:error="errors.acronym = $event"
            :placeholder="errors.acronym ? errors.name : 'Kürzel'"
            :classes="'uppercase placeholder:normal-case'"
            :maxLength="5"
            aria-label="acronym" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Kunde" id="company" />
          <InputSelect
            id="company"
            placeholder="Bitte wählen..."
            v-model="form.company"
            :options="companies"
            :error="errors.company"
          />
        </InputGroup>
      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary label="Speichern" :disabled="isSaving" />
        </ButtonGroup>
      </div>

    </form>

  </Slide>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue';
import { getUserCompanies } from '@/services/api/user';
import { createArchive, updateArchive } from '@/services/api/archive';
import { useToastStore } from '@/components/toast/stores/toast';
import { useArchiveStore } from '@/stores/archive';
import ImageUpload from '@/components/media/upload/Image.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputSelect from '@/components/forms/Select.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

const toast = useToastStore();
const archiveStore = useArchiveStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  },
  componentProps: {
    type: Object,
    default: () => {}
  },
});

const companies = ref([]);
const isLoading = ref(false);
const isSaving = ref(false);

const form = ref({
  uuid: '',
  name: 'Marcelito',
  acronym: 'MTO',
  company: '',
  image: null
});

const errors = ref({
  name: null,
  acronym: null,
  company: null,
  image: null
});

// watch for changes if "isActive"
watch(() => props.isActive, (newVal) => {
  if (!newVal) {
    resetView();
  }
});

onMounted(async () => {
  try {
    isLoading.value = true;
    await fetchCompanies();

    // If we have an archiveId from props or from store, use it
    if (props.componentProps?.archive) {
      archiveStore.setArchiveId(props.componentProps.archive);
    }

    // Initialize form with existing data if editing
    if (archiveStore.currentArchiveId) {
      form.value = {
        uuid: archiveStore.archiveData.uuid,
        name: archiveStore.archiveData.name,
        acronym: archiveStore.archiveData.acronym,
        company: archiveStore.archiveData.company?.uuid ?? '',
        image: archiveStore.archiveData.image
      };
    }

  } 
  catch (error) {
    toast.show('Fehler beim Laden der Kartei.', 'error');
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;

    if (!archiveStore.currentArchiveId) {
      const response = await createArchive(form.value);
      archiveStore.setArchiveId(response.uuid);
      form.value.uuid = response.uuid;
      form.value.image = [response.image];
      toast.show('Kartei erfolgreich gespeichert.', 'success');
    } 
    else {
      const response = await updateArchive(form.value);
      toast.show('Kartei erfolgreich aktualisiert.', 'success');
    }

  } 
  catch (error) {
    errors.value = {
      name: error.response?.data?.errors?.name?.[0],
      acronym: error.response?.data?.errors?.acronym?.[0],
      image: error.response?.data?.errors?.image?.[0]
    }
    toast.show('Fehler beim Speichern der Kartei.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

const fetchCompanies = async () => {
  const response = await getUserCompanies();
  if (Array.isArray(response)) {
    companies.value = response;
  } else if (response?.data) {
    companies.value = response.data.map(company => ({
      value: company.uuid,
      label: company.name
    }));
  }
};

const resetView = () => {
  errors.value = {
    name: null,
    acronym: null,
    company: null,
    image: null
  };
};

</script>
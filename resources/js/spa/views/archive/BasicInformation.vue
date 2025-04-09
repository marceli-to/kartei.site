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
import { useRoute, useRouter } from 'vue-router';
import { handleApiError } from '@/services/api/error'
import { getUserCompanies } from '@/services/api/user';
import { getArchive, createArchive, updateArchive } from '@/services/api/archive';
import { useToastStore } from '@/components/toast/stores/toast';
import ImageUpload from '@/components/media/upload/Image.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputSelect from '@/components/forms/Select.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  },
});

const companies = ref([]);
const isLoading = ref(false);
const isSaving = ref(false);

const route = useRoute();
const router = useRouter();
const uuid = ref(route.params.uuid || null);

const form = ref({
  uuid: '',
  name: '',
  acronym: '',
  company: '',
  image: null
});

const errors = ref({
  name: null,
  acronym: null,
  company: null,
  image: null
});

// Watch for changes in route.params.uuid
watch(() => route.params.uuid, (newUuid) => {
  if (newUuid !== uuid.value) {
    uuid.value = newUuid || null;
    if (uuid.value) {
      fetchArchive();
    }
  }
}, { immediate: true });

// Watch for changes if "isActive"
watch(() => props.isActive, (newVal) => {
  if (!newVal) {
    resetView();
  }
});

onMounted(async () => {
  try {
    isLoading.value = true;
    await fetchCompanies();

    // If we have a uuid from the route, fetch the archive
    if (uuid.value) {
      await fetchArchive();
    }
  } 
  catch (error) {
    console.error(error);
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;

    if (!uuid.value) {
      const response = await createArchive(form.value);
      uuid.value = response.uuid;
      form.value.uuid = response.uuid;
      form.value.image = [response.image];

      // Push the new uuid to route here:
      router.push({ 
        name: 'archiveSettings', 
        params: { uuid: response.uuid }
      });

      toast.show('Kartei erfolgreich gespeichert.', 'success');
    } 
    else {
      const response = await updateArchive(form.value);
      toast.show('Kartei erfolgreich aktualisiert.', 'success');
    }
  } 
  catch (error) {
    console.error(error);
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

const fetchArchive = async () => {
  if (!uuid.value) return;
  
  try {
    const response = await getArchive(uuid.value);
    if (response) {
      form.value = {
        uuid: response.uuid,
        name: response.name,
        acronym: response.acronym,
        company: response.company?.uuid ?? '',
        image: [response.image]
      };
    }
  } 
  catch (error) {
    handleApiError(error);
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
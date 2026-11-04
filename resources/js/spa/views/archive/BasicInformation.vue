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
            :allowedTypes="['image/*']"
            :multiple="false"
            v-model="form.image"
            :existingImages="form.image"
            :error="errors.image"
          />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Name" id="name" required :error="errors.name" />
          <InputText
            v-model="form.name"
            id="name"
            :error="errors.name"
            @update:error="errors.name = $event"
            placeholder="Name"
            aria-label="Name" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Kürzel (max. 5 Zeichen)" id="acronym" required :error="errors.acronym" />
          <InputText
            v-model="form.acronym"
            id="acronym"
            :error="errors.acronym"
            @update:error="errors.acronym = $event"
            placeholder="Kürzel"
            :classes="'uppercase placeholder:normal-case'"
            :maxLength="5"
            aria-label="Kürzel" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Kunde" id="company" :error="errors.company" />
          <InputSelect
            id="company"
            placeholder="Bitte wählen..."
            v-model="form.company"
            :options="companies"
            :error="errors.company"
            @update:error="errors.company = $event"
          />
        </InputGroup>
      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <AppButton label="Speichern" :disabled="isSaving" />
        </ButtonGroup>
      </div>

    </form>

  </Slide>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { notifyError } from '@/services/api/error'
import { getUserCompanies } from '@/services/api/company';
import { getArchive, createArchive, updateArchive } from '@/services/api/archive';
import { useToastStore } from '@/stores/toast';
import { useUserStore } from '@/stores/user';
import { useFormErrors } from '@/composables/useFormErrors';

import ImageUpload from '@/components/media/upload/Image.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputSelect from '@/components/forms/Select.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import Slide from '@/components/slider/Slide.vue';
import AppButton from '@/components/buttons/Button.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  },
});

const companies = ref([]);
const isLoading = ref(true);
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

const { errors, setErrors, clearErrors } = useFormErrors(['name', 'acronym', 'company', 'image']);

// No route.params.uuid watcher: pages are keyed by route.path, so a new
// uuid (e.g. after creating) mounts a new page that loads it in onMounted

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
    notifyError(error, 'Die Kartei konnte nicht geladen werden.');
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    clearErrors();

    if (!uuid.value) {
      const response = await createArchive(form.value);
      // Saved: from here on nothing may report a failure, or the user
      // would save again and create the archive twice
      uuid.value = response.uuid;
      form.value.uuid = response.uuid;
      form.value.image = [response.image];
      toast.show('Kartei erfolgreich gespeichert.', 'success');

      // The per-archive permissions are loaded once; fetch them again so
      // the route guard and the slides know about the new archive
      await useUserStore().fetchPermissions();

      router.push({ 
        name: 'archiveSettings', 
        params: { uuid: response.uuid }
      });
    } 
    else {
      await updateArchive(form.value);
      toast.show('Kartei erfolgreich aktualisiert.', 'success');
    }
  } 
  catch (error) {
    setErrors(error);
    notifyError(error, 'Fehler beim Speichern der Kartei.');
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
    // 403/404 already went to the error page via the interceptor
    notifyError(error, 'Die Kartei konnte nicht geladen werden.');
  }
};

const fetchCompanies = async () => {
  const response = await getUserCompanies();
  if (Array.isArray(response)) {
    companies.value = response;
  } 
  else if (response?.data) {
    companies.value = response.data.map(company => ({
      value: company.uuid,
      label: company.name
    }));
  }
};

const resetView = () => {
  clearErrors();
};
</script>
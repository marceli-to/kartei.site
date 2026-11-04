<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col justify-between"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-48">
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Bezeichnung / Firma" id="company_name" required :error="errors.name" />
          <InputText
            v-model="form.name"
            id="company_name"
            :error="errors.name"
            @update:error="errors.name = $event"
            placeholder="Bezeichnung / Firma"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Strasse" id="company_street" required :error="errors.street" />
          <InputText
            v-model="form.street"
            id="company_street"
            :error="errors.street"
            @update:error="errors.street = $event"
            placeholder="Strasse"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Hausnummer" id="company_street_number" :error="errors.street_number" />
          <InputText
            v-model="form.street_number"
            id="company_street_number"
            :error="errors.street_number"
            @update:error="errors.street_number = $event"
            placeholder="Hausnummer" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="PLZ" id="company_zip" required :error="errors.zip" />
          <InputText
            v-model="form.zip"
            id="company_zip"
            :error="errors.zip"
            @update:error="errors.zip = $event"
            placeholder="PLZ"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Ort" id="company_city" required :error="errors.city" />
          <InputText
            v-model="form.city"
            id="company_city"
            :error="errors.city"
            @update:error="errors.city = $event"
            placeholder="Ort"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Land" id="company_country" required :error="errors.country" />
          <InputSelect
            id="company_country"
            v-model="form.country"
            :options="countries"
            :error="errors.country"
            @update:error="errors.country = $event"
          />
        </InputGroup>
      </div>
      <div>
        <ButtonPrimary label="Löschen" variant="danger" type="button" @click.prevent="showDeleteDialog" />
      </div>
    </div>
    <ButtonGroup>
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary type="button" @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { countries } from '@/config/countries';
import { updateUserCompany, deleteUserCompany } from '@/services/api/user';
import { useToastStore } from '@/components/toast/stores/toast';
import { useFormErrors } from '@/composables/useFormErrors';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputSelect from '@/components/forms/Select.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputGroup from '@/components/forms/Group.vue';

const toast = useToastStore();
const dialogStore = useDialogStore();
const emit = defineEmits(['success', 'cancel', 'delete']);

const isLoading = ref(true); // Start with loading state true
const isSaving = ref(false);

const props = defineProps({
  company: {
    type: Object,
    required: true
  }
});

const form = ref({
  name: '',
  street: '',
  street_number: '',
  zip: '',
  city: '',
  country: 'Schweiz',
});

const { errors, setErrors, clearErrors } = useFormErrors(['name', 'street', 'street_number', 'zip', 'city', 'country']);

// Initialize form with company data when component is mounted
onMounted(async () => {
  try {
    mapCompanyToForm();
  }
  finally {
    isLoading.value = false;
  }
});

// Map company data to form without using a watcher
const mapCompanyToForm = () => {
  form.value = {
    uuid: props.company.uuid,
    name: props.company.name || '',
    street: props.company.street || '',
    street_number: props.company.street_number || '',
    zip: props.company.zip || '',
    city: props.company.city || '',
    country: props.company.country || 'Schweiz',
  };
};

const showDeleteDialog = () => {
  dialogStore.show({
    title: `Möchten Sie die Firma "${form.value.name}" wirklich löschen?`,
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
    onConfirm: handleDelete,
    onCancel: () => {}
  });
};

// The parent removes the company from the list and confirms, so only emit once it's gone
const handleDelete = async () => {
  try {
    await deleteUserCompany(props.company.uuid);
    emit('delete', props.company.uuid);
  }
  catch (error) {
    console.error(error);
    toast.show('Fehler beim Löschen der Firma.', 'error');
  }
};

const submit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    const response = await updateUserCompany(form.value);
    emit('success', response.data);
  }
  catch (error) {
    setErrors(error);
    toast.show('Fehler beim Speichern der Firma.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};
</script>
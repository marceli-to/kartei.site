<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col justify-between"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-48">
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Bezeichnung / Firma" id="name" required />
          <InputText
            v-model="form.name"
            id="name"
            :error="errors.name"
            @update:error="errors.name = $event"
            :placeholder="errors.name ? errors.name : 'Bezeichnung / Firma'"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Strasse" id="street" required />
          <InputText
            v-model="form.street"
            id="street"
            :error="errors.street"
            @update:error="errors.street = $event"
            :placeholder="errors.street ? errors.street : 'Strasse'"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Hausnummer" id="street_number" />
          <InputText
            v-model="form.street_number"
            id="street_number"
            :error="errors.street_number"
            @update:error="errors.street_number = $event"
            :placeholder="errors.street_number ? errors.street_number : 'Hausnummer'" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="PLZ" id="zip" required />
          <InputText
            v-model="form.zip"
            id="zip"
            :error="errors.zip"
            @update:error="errors.zip = $event"
            :placeholder="errors.zip ? errors.zip : 'PLZ'"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Ort" id="city" required />
          <InputText
            v-model="form.city"
            id="city"
            :error="errors.city"
            @update:error="errors.city = $event"
            :placeholder="errors.city ? errors.city : 'Ort'"
            required />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Land" id="country" required />
          <InputSelect
            id="country"
            v-model="form.country"
            :options="countries"
            :error="errors.country"
          />
        </InputGroup>
      </div>
      <div>
        <ButtonPrimary label="Löschen" variant="danger" type="button" @click.prevent="showDeleteDialog" />
      </div>
    </div>
    <ButtonGroup>
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { countries } from '@/data/countries';
import { updateUserCompany, deleteUserCompany } from '@/services/api/user';
import { useToastStore } from '@/components/toast/stores/toast';
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

const errors = ref({
  name: null,
  street: null,
  street_number: null,
  zip: null,
  city: null,
  country: null,
});

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
    onConfirm: () => {
      handleDelete();
    },
    onCancel: () => {
    }
  });
};

const handleDelete = () => {
  deleteUserCompany(props.company.uuid);
  emit('delete', props.company.uuid);
};

const submit = async () => {
  try {
    isSaving.value = true;
    const response = await updateUserCompany(form.value);
    emit('success', response.data);
  }
  catch (error) {
    errors.value = {
      name: error.response?.data?.errors?.name?.[0],
      street: error.response?.data?.errors?.street?.[0],
      street_number: error.response?.data?.errors?.street_number?.[0],
      zip: error.response?.data?.errors?.zip?.[0],
      city: error.response?.data?.errors?.city?.[0],
      country: error.response?.data?.errors?.country?.[0],
    };
    toast.show('Fehler beim Speichern der Firma.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};
</script>
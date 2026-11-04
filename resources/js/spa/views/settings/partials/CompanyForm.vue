<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col justify-between">
    <div class="flex flex-col gap-y-48">
      <AddressFields :form="form" :errors="errors" idPrefix="company_" nameField="name" />
      <div v-if="company">
        <AppButton label="Löschen" variant="danger" type="button" @click="showDeleteDialog" />
      </div>
    </div>
    <ButtonGroup>
      <AppButton type="submit" label="Speichern" :loading="isSaving" />
      <AppButton type="button" @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { createUserCompany, updateUserCompany, deleteUserCompany } from '@/services/api/company';
import { notifyError } from '@/services/api/error';
import { useDialogStore } from '@/stores/dialog';
import { useFormErrors } from '@/composables/useFormErrors';
import AddressFields from '@/components/forms/AddressFields.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import AppButton from '@/components/buttons/Button.vue';

// Without company it creates one; the parent shows the toasts and updates the list
const props = defineProps({
  company: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['success', 'cancel', 'delete']);
const dialogStore = useDialogStore();
const isSaving = ref(false);

const form = ref({
  uuid: props.company?.uuid,
  name: props.company?.name || '',
  street: props.company?.street || '',
  street_number: props.company?.street_number || '',
  zip: props.company?.zip || '',
  city: props.company?.city || '',
  country: props.company?.country || 'Schweiz',
});

const { errors, setErrors, clearErrors } = useFormErrors(['name', 'street', 'street_number', 'zip', 'city', 'country']);

const submit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    const response = props.company
      ? await updateUserCompany(form.value)
      : await createUserCompany(form.value);
    emit('success', response.data);
  }
  catch (error) {
    setErrors(error);
    notifyError(error, props.company ? 'Fehler beim Speichern der Firma.' : 'Fehler beim Erstellen der Firma.');
  }
  finally {
    isSaving.value = false;
  }
};

// The parent removes the company from the list and confirms, so only emit once it's gone
const showDeleteDialog = () => {
  dialogStore.confirmDelete({
    title: `Möchten Sie die Firma "${props.company.name}" wirklich löschen?`,
    action: () => deleteUserCompany(props.company.uuid),
    then: () => emit('delete', props.company.uuid),
    failure: 'Fehler beim Löschen der Firma.',
  });
};
</script>

<template>
  <form
    class="flex h-full w-full flex-col justify-between gap-y-32"
    @submit.prevent="submit">
    <!-- Editing shows the fields in a box headed by the company; creating shows them plain -->
    <component
      :is="company ? EditBox : 'div'"
      v-bind="company ? { title: company.name } : {}"
      @close="$emit('cancel')">
      <div class="flex flex-col gap-y-48">
        <AddressFields
          v-model:form="form"
          v-model:errors="errors"
          id-prefix="company_"
          name-field="name" />
        <AppButton
          v-if="company"
          label="Kunde löschen"
          type="button"
          @click="showDeleteDialog" />
      </div>
    </component>
    <ButtonGroup>
      <AppButton
        type="submit"
        label="Speichern"
        :loading="isSaving" />
      <AppButton
        type="button"
        label="Abbrechen"
        @click="$emit('cancel')" />
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
import EditBox from '@/components/EditBox.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import AppButton from '@/components/buttons/Button.vue';

// Without company it creates one; the parent shows the toasts and updates the list
const props = defineProps({
  company: {
    type: Object,
    default: null,
  },
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
  country: props.company?.country || 'CH',
});

const { errors, setErrors, clearErrors } = useFormErrors(['name', 'street', 'street_number', 'zip', 'city', 'country']);

const submit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    const response = props.company ? await updateUserCompany(form.value) : await createUserCompany(form.value);
    emit('success', response.data);
  } catch (error) {
    setErrors(error);
    notifyError(error, props.company ? 'Fehler beim Speichern des Kunden.' : 'Fehler beim Erstellen des Kunden.');
  } finally {
    isSaving.value = false;
  }
};

// The parent removes the company from the list and confirms, so only emit once it's gone
const showDeleteDialog = () => {
  dialogStore.confirmDelete({
    title: `Möchten Sie den Kunden "${props.company.name}" wirklich löschen?`,
    action: () => deleteUserCompany(props.company.uuid),
    then: () => emit('delete', props.company.uuid),
    failure: 'Fehler beim Löschen des Kunden.',
  });
};
</script>

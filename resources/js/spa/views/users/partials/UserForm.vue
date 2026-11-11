<template>
  <form
    v-if="!isLoading"
    class="flex h-full w-full flex-col justify-between gap-y-32"
    @submit.prevent="submit">
    <div class="flex flex-col gap-y-32">
      <div class="flex flex-col gap-y-20">
        <InputGroup
          v-for="field in FIELDS"
          :key="field.key">
          <InputLabel
            :id="`user_${field.key}`"
            :label="field.label"
            required
            :error="errors[field.key]" />
          <InputText
            :id="`user_${field.key}`"
            v-model="form[field.key]"
            :error="errors[field.key]"
            :placeholder="field.label"
            @update:error="errors[field.key] = $event" />
        </InputGroup>
      </div>
      <AppButton
        v-if="uuid"
        label="Benutzer löschen"
        type="button"
        @click="showDeleteDialog" />
    </div>
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
import { ref, onMounted } from 'vue';
import { findUser, createUser, updateUser, deleteUser } from '@/services/api/archiveUser';
import { notifyError } from '@/services/api/error';
import { useToastStore } from '@/stores/toast';
import { useDialogStore } from '@/stores/dialog';
import { useFormErrors } from '@/composables/useFormErrors';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputGroup from '@/components/forms/Group.vue';
import AppButton from '@/components/buttons/Button.vue';

// Without uuid it creates a user (Index.vue then asks for the permissions); editing sits in UserEdit's box
const props = defineProps({
  uuid: {
    type: [String, Number],
    default: null,
  },
});

const emit = defineEmits(['success', 'cancel', 'user-deleted']);

const FIELDS = [
  { key: 'firstname', label: 'Vorname' },
  { key: 'name', label: 'Nachname' },
  { key: 'email', label: 'E-Mail' },
];

const toast = useToastStore();
const dialogStore = useDialogStore();
const isLoading = ref(!!props.uuid);
const isSaving = ref(false);
const userData = ref(null);

const form = ref({
  firstname: '',
  name: '',
  email: '',
});

const { errors, setErrors, clearErrors } = useFormErrors(FIELDS.map((field) => field.key));

onMounted(async () => {
  if (!props.uuid) return;
  try {
    userData.value = await findUser(props.uuid);
    form.value = {
      firstname: userData.value.firstname || '',
      name: userData.value.name || '',
      email: userData.value.email || '',
    };
  } catch (error) {
    notifyError(error, 'Die Benutzerdaten konnten nicht geladen werden.');
  } finally {
    isLoading.value = false;
  }
});

const submit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    const response = props.uuid ? await updateUser(props.uuid, { ...form.value }) : await createUser({ ...form.value });
    toast.show(props.uuid ? 'Benutzer/in erfolgreich aktualisiert.' : 'Benutzer/in erfolgreich erstellt.', 'success');
    emit('success', response);
  } catch (error) {
    setErrors(error);
    notifyError(
      error,
      props.uuid ? 'Fehler beim Aktualisieren des Benutzers.' : 'Fehler beim Erstellen des Benutzers.',
    );
  } finally {
    isSaving.value = false;
  }
};

const showDeleteDialog = () => {
  dialogStore.confirmDelete({
    title: `Möchten Sie den Benutzer/in "${form.value.firstname} ${form.value.name}" wirklich löschen?`,
    action: () => deleteUser(props.uuid),
    then: () => emit('user-deleted', props.uuid),
    success: 'Benutzer/in erfolgreich gelöscht.',
    failure: 'Fehler beim Löschen des Benutzers.',
  });
};
</script>

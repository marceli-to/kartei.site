<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col gap-y-32 justify-between" 
    v-if="!isLoading">
    <div class="flex flex-col gap-y-32">
      <div class="flex flex-col gap-y-20">
        <InputGroup v-for="field in FIELDS" :key="field.key">
          <InputLabel :label="field.label" :id="`user_${field.key}`" required :error="errors[field.key]" />
          <InputText 
            v-model="form[field.key]" 
            :id="`user_${field.key}`" 
            :error="errors[field.key]" 
            @update:error="errors[field.key] = $event" 
            :placeholder="field.label" />
        </InputGroup>
      </div>
      <div v-if="uuid" class="flex flex-col gap-y-48">
        <Action 
          type="button"
          label="Rechte"
          :icon="{ name: 'ChevronRight' }"
          @click="$emit('user-selected-permissions', userData)" />
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
import Action from '@/components/buttons/Action.vue';
import AppButton from '@/components/buttons/Button.vue';

// Without uuid it creates a user (Index.vue then asks for the permissions)
const props = defineProps({
  uuid: {
    type: [String, Number],
    default: null
  }
});

const emit = defineEmits([
  'success', 
  'cancel', 
  'user-selected-permissions', 
  'user-deleted'
]);

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

const { errors, setErrors, clearErrors } = useFormErrors(FIELDS.map(field => field.key));

onMounted(async () => {
  if (!props.uuid) return;
  try {
    userData.value = await findUser(props.uuid);
    form.value = {
      firstname: userData.value.firstname || '',
      name: userData.value.name || '',
      email: userData.value.email || '',
    };
  }
  catch (error) {
    notifyError(error, 'Die Benutzerdaten konnten nicht geladen werden.');
  }
  finally {
    isLoading.value = false;
  }
});

const submit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    const response = props.uuid
      ? await updateUser(props.uuid, { ...form.value })
      : await createUser({ ...form.value });
    toast.show(props.uuid ? 'Benutzer/in erfolgreich aktualisiert.' : 'Benutzer/in erfolgreich erstellt.', 'success');
    emit('success', response);
  } 
  catch (error) {
    setErrors(error);
    notifyError(error, props.uuid ? 'Fehler beim Aktualisieren des Benutzers.' : 'Fehler beim Erstellen des Benutzers.');
  }
  finally {
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

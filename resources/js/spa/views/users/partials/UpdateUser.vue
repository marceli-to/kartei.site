<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col gap-y-32 justify-between" 
    v-if="!isLoading">
    <div class="flex flex-col gap-y-32">
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Vorname" id="firstname" required />
          <InputText 
            v-model="form.firstname" 
            id="firstname" 
            :error="errors.firstname" 
            @update:error="errors.firstname = $event" 
            :placeholder="errors.firstname ? errors.firstname : 'Vorname'" 
            required 
          />
        </InputGroup>

        <InputGroup>
          <InputLabel label="Nachname" id="name" required />
          <InputText 
            v-model="form.name" 
            id="name" 
            :error="errors.name" 
            @update:error="errors.name = $event" 
            :placeholder="errors.name ? errors.name : 'Nachname'" 
            required 
          />
        </InputGroup>

        <InputGroup>
          <InputLabel label="E-Mail" id="email" required />
          <InputText 
            v-model="form.email" 
            id="email" 
            :error="errors.email" 
            @update:error="errors.email = $event" 
            :placeholder="errors.email ? errors.email : 'E-Mail'" 
            required 
          />
        </InputGroup>
      </div>
      <div class="flex flex-col gap-y-48">
        <Action 
          type="button"
          label="Rechte"
          :icon="{ name: 'ChevronRight' }"
          @click="$emit('user-selected-permissions', userData)" />
        <ButtonPrimary label="Löschen" variant="danger" type="button" @click.prevent="showDeleteDialog" />
      </div>
    </div>
    <ButtonGroup>
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { findUser, updateUser, deleteUser } from '@/services/api/archiveUser';
import { useToastStore } from '@/components/toast/stores/toast';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputGroup from '@/components/forms/Group.vue';
import Action from '@/components/buttons/Action.vue';

const toast = useToastStore();
const dialogStore = useDialogStore();
const emit = defineEmits([
  'success', 
  'cancel', 
  'user-selected-permissions', 
  'user-deleted'
]);

const props = defineProps({
  uuid: {
    type: [String, Number],
    required: true
  }
});

const isLoading = ref(true);
const isSaving = ref(false);
const userData = ref('');

const form = ref({
  firstname: '',
  name: '',
  email: '',
});

const errors = ref({
  firstname: null,
  name: null,
  email: null,
});

onMounted(async () => {
  try {
    isLoading.value = true;
    userData.value = await findUser(props.uuid);
    
    // Populate form with user data
    form.value = {
      firstname: userData.value.firstname || '',
      name: userData.value.name || '',
      email: userData.value.email || '',
    };
  }
  catch (error) {
    console.error('Failed to fetch user data:', error);
  }
  finally {
    isLoading.value = false;
  }
});

const submit = async () => {
  try {
    isSaving.value = true;
    
    // Reset errors before submission
    errors.value = {
      firstname: null,
      name: null,
      email: null,
    };
    
    const userData = { ...form.value };
    const response = await updateUser(props.uuid, userData);
    
    toast.show('Benutzer/in erfolgreich aktualisiert.', 'success');
    emit('success', response);
  } catch (error) {
    errors.value = {
      firstname: error.response?.data?.errors?.firstname?.[0],
      name: error.response?.data?.errors?.name?.[0],
      email: error.response?.data?.errors?.email?.[0],
    };
    
    toast.show('Fehler beim Aktualisieren des Benutzers.', 'error');
  } finally {
    isSaving.value = false;
  }
};

const showDeleteDialog = () => {
  dialogStore.show({
    title: `Möchten Sie den Benutzer/in "${form.value.firstname} ${form.value.name}" wirklich löschen?`,
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
  deleteUser(props.uuid);
  emit('user-deleted', props.uuid);
};
</script>
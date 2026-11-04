<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col justify-between">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputLabel label="Vorname" id="user_firstname" required :error="errors.firstname" />
        <InputText
          v-model="form.firstname"
          id="user_firstname"
          :error="errors.firstname"
          @update:error="errors.firstname = $event"
          placeholder="Vorname"
          required />
      </InputGroup>
      <InputGroup>
        <InputLabel label="Nachname" id="user_name" required :error="errors.name" />
        <InputText
          v-model="form.name"
          id="user_name"
          :error="errors.name"
          @update:error="errors.name = $event"
          placeholder="Nachname"
          required />
      </InputGroup>
      <InputGroup>
        <InputLabel label="E-Mail" id="user_email" required :error="errors.email" />
        <InputText
          v-model="form.email"
          id="user_email"
          :error="errors.email"
          @update:error="errors.email = $event"
          placeholder="E-Mail"
          required />
      </InputGroup>
    </div>
    <ButtonGroup>
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary type="button" @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { createUser } from '@/services/api/archiveUser';
import { useToastStore } from '@/stores/toast';
import { useFormErrors } from '@/composables/useFormErrors';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputGroup from '@/components/forms/Group.vue';
import { notifyError } from '@/services/api/error';

const toast = useToastStore();
const emit = defineEmits(['success', 'cancel']);

const isSaving = ref(false);

const form = ref({
  firstname: '',
  name: '',
  email: '',
});

const { errors, setErrors, clearErrors } = useFormErrors(['firstname', 'name', 'email']);

const submit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    const userData = {
      ...form.value
    };
    const response = await createUser(userData);
    toast.show('Benutzer/in erfolgreich erstellt.', 'success');
    emit('success', response);
  }
  catch (error) {
    setErrors(error);
    notifyError(error, 'Fehler beim Erstellen des Benutzers.');
  }
  finally {
    isSaving.value = false;
  }
};
</script>
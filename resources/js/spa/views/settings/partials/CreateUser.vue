<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col justify-between">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputLabel label="Vorname" id="firstname" required />
        <InputText
          v-model="form.firstname"
          id="firstname"
          :error="errors.firstname"
          @update:error="errors.firstname = $event"
          :placeholder="errors.firstname ? errors.firstname : 'Vorname'"
          required />
      </InputGroup>
      <InputGroup>
        <InputLabel label="Nachname" id="name" required />
        <InputText
          v-model="form.name"
          id="name"
          :error="errors.name"
          @update:error="errors.name = $event"
          :placeholder="errors.name ? errors.name : 'Nachname'"
          required />
      </InputGroup>
      <InputGroup>
        <InputLabel label="E-Mail" id="email" required />
        <InputText
          v-model="form.email"
          id="email"
          :error="errors.email"
          @update:error="errors.email = $event"
          :placeholder="errors.email ? errors.email : 'E-Mail'"
          required />
      </InputGroup>
    </div>
    <ButtonGroup>
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { createUser } from '@/services/api/archiveUser';
import { useToastStore } from '@/components/toast/stores/toast';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputGroup from '@/components/forms/Group.vue';

const toast = useToastStore();
const emit = defineEmits(['success', 'cancel']);

const isSaving = ref(false);

const form = ref({
  firstname: 'Miyu',
  name: 'Morf',
  email: 'Miyu.Morf@kartei.site',
});

const errors = ref({
  firstname: null,
  name: null,
  email: null,
});

const submit = async () => {
  try {
    isSaving.value = true;
    const userData = {
      ...form.value
    };
    const response = await createUser(userData);
    toast.show('Benutzer/in erfolgreich erstellt.', 'success');
    emit('success', response);
  }
  catch (error) {
    errors.value = {
      firstname: error.response?.data?.errors?.firstname?.[0],
      name: error.response?.data?.errors?.name?.[0],
      email: error.response?.data?.errors?.email?.[0],
    };
    toast.show('Fehler beim Erstellen des Benutzers.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};
</script>
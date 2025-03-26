<template>
  <form 
    @submit.prevent="submit" 
    class="w-full h-full flex flex-col justify-between" 
    v-if="!isLoading">
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

    <ButtonGroup>
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { findUser, updateUser } from '@/services/api/archiveUser';
import { useToastStore } from '@/components/toast/stores/toast';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InputGroup from '@/components/forms/Group.vue';

const props = defineProps({
  userId: {
    type: [String, Number],
    required: true
  }
});

const toast = useToastStore();
const emit = defineEmits(['success', 'cancel']);

const isLoading = ref(true);
const isSaving = ref(false);

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
    const userData = await findUser(props.userId);
    
    // Populate form with user data
    form.value = {
      firstname: userData.firstname || '',
      name: userData.name || '',
      email: userData.email || '',
    };
  } catch (error) {
    console.error('Failed to fetch user data:', error);
    toast.show('Fehler beim Laden der Benutzerdaten.', 'error');
  } finally {
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
    const response = await updateUser(props.userId, userData);
    
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
</script>
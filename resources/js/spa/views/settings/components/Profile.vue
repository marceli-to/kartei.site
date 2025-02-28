<template>
  <Slide :pull="true">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Info box -->
      <template v-if="infoBox.isActive">
        <InfoBox>
          <InfoProfile />
        </InfoBox>
      </template>

      <!-- Form elements -->
      <div class="flex flex-col gap-y-48">

        <!-- Personal details -->
        <div class="flex flex-col gap-y-20">
          <InputGroup>
            <InputLabel label="Vorname" id="firstname" required />
            <InputText
              v-model="form.firstname"
              id="firstname"
              :error="errors.firstname"
              @update:error="errors.firstname = $event"
              :placeholder="errors.firstname ? errors.firstname : 'Vorname'"
              aria-label="Vorname" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Name" id="name" required />
            <InputText
              v-model="form.name"
              id="name"
              :error="errors.name"
              @update:error="errors.name = $event"
              :placeholder="errors.name ? errors.name : 'Nachname'"
              aria-label="Nachname" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="E-Mail-Adresse" id="email" required />
            <InputText
              v-model="form.email"
              id="email"
              :error="errors.email"
              @update:error="errors.email = $event"
              :placeholder="errors.email ? errors.email : 'E-Mail-Adresse'"
              aria-label="E-Mail-Adresse" />
          </InputGroup>
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-y-20">
          <template v-if="authErrors.length > 0">
            <div class="text-sm font-muoto-italic text-flame">
              <div v-for="(error, index) in authErrors" :key="index">
                {{ error }}
              </div>
            </div>
          </template>
          <InputGroup>
            <InputLabel label="Passwort ändern" id="current_password" />
            <div class="flex flex-col gap-y-8">
              <InputPassword
                id="current_password"
                v-model="auth.current_password"
                :error="errors.current_password"
                @update:error="errors.current_password = $event"
                placeholder="Aktuelles Passwort"
                aria-label="Aktuelles Passwort" />
              <InputPassword
                id="new_password"
                v-model="auth.new_password"
                :error="errors.new_password"
                @update:error="errors.new_password = $event"
                placeholder="Neues Passwort"
                aria-label="Neues Passwort" />
              <InputPassword
                id="new_password_confirmation"
                v-model="auth.new_password_confirmation"
                :error="errors.new_password_confirmation"
                @update:error="errors.new_password_confirmation = $event"
                placeholder="Neues Passwort wiederholen"
                aria-label="Passwort wiederholen" />
              <ButtonAuth 
                type="button"
                label="Passwort ändern"
                aria-label="Passwort ändern"
                @click.stop.prevent="handleAuthSubmit" />
              <div class="text-sm font-muoto-italic text-graphite pl-8">
                Das Passwort muss mindestens 8 Zeichen umfassen sowie Gross- und Kleinbuchstaben, Zahlen und Sonderzeichen enthalten.
              </div>
            </div>
          </InputGroup>
        </div>

      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary label="Speichern" :disabled="isSaving" />
        </ButtonGroup>
      </div>

    </form>
  </Slide>
</template>
<script setup>
import { ref, onMounted, toRef } from 'vue';
import { getUserProfile, updateUserProfile, updatePassword } from '@/services/api/user';
import { useToastStore } from '@/components/toast/stores/toast';
import { useInfoBox } from '@/components/infobox/composables/useInfoBox';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputPassword from '@/components/forms/Password.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonAuth from '@/components/buttons/Auth.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import InfoProfile from '@/views/settings/components/ProfileInfo.vue';
import Slide from '@/components/slider/Slide.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const isLoading = ref(false);
const isSaving = ref(false);
const hasInfo = ref(true);

const infoBox = useInfoBox({
  isActive: toRef(props, 'isActive'),
  condition: hasInfo
});

const form = ref({
  uuid: null,
  firstname: '',
  name: '',
  email: '',
});

const auth = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

const errors = ref({
  firstname: null,
  name: null,
  email: null,
});

const authErrors = ref([]);

onMounted(async () => {
  try {
    isLoading.value = true;
    const userData = await getUserProfile();
    if (userData) {
      form.value.uuid = userData.uuid;
      form.value.firstname = userData.firstname;
      form.value.name = userData.name;
      form.value.email = userData.email;
    }
  } 
  catch (error) {
    console.error(error);
  } 
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    const userData = await updateUserProfile(form.value);
    if (userData.email_changed) {
      toast.show('E-Mail-Adresse wurde geändert, wir haben einen neuen Verifizierungslink geschickt.', 'success');
    }
    else {
      toast.show('Änderungen gespeichert.', 'success');
    }
  } 
  catch (error) {
    errors.value = {
      firstname: error.response?.data?.errors?.firstname?.[0],
      name: error.response?.data?.errors?.name?.[0],
      email: error.response?.data?.errors?.email?.[0],
    };
  }
  finally {
    isSaving.value = false;
  }
};

const handleAuthSubmit = async () => {
  try {
    authErrors.value = []; // Clear previous errors
    isLoading.value = true;
    const passwordData = await updatePassword(auth.value);
    toast.show('Passwort wurde erfolgreich geändert.', 'success');
    auth.value.current_password = '';
    auth.value.new_password = '';
    auth.value.new_password_confirmation = '';
  } 
  catch (error) {
    const errorData = error.response?.data?.errors || {};
    
    // Set individual field errors
    errors.value.current_password = errorData.current_password?.[0] || null;
    errors.value.new_password = errorData.new_password?.[0] || null;
    errors.value.new_password_confirmation = errorData.new_password_confirmation?.[0] || null;
    
    // Collect all errors into the array
    authErrors.value = [];
    
    // Add current_password errors
    if (errorData.current_password) {
      authErrors.value.push(...errorData.current_password);
    }
    
    // Add new_password errors
    if (errorData.new_password) {
      authErrors.value.push(...errorData.new_password);
    }
    
    // Add new_password_confirmation errors
    if (errorData.new_password_confirmation) {
      authErrors.value.push(...errorData.new_password_confirmation);
    }

    toast.show('Es ist ein Fehler aufgetreten.', 'error', 5000);
  }
  finally {
    isLoading.value = false;
  }
};

</script>
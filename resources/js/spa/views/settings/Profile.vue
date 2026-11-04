<template>
  <Slide :pull="true">
    <form
      v-if="!isLoading"
      class="flex h-full w-full flex-col justify-between"
      @submit.prevent="handleSubmit">
      <!-- Info box -->
      <template v-if="isInfoOpen">
        <InfoBox>
          <InfoProfile />
        </InfoBox>
      </template>

      <!-- Form elements -->
      <div class="flex flex-col gap-y-48">
        <!-- Personal details -->
        <div class="flex flex-col gap-y-20">
          <InputGroup>
            <InputLabel
              id="firstname"
              label="Vorname"
              required
              :error="errors.firstname" />
            <InputText
              id="firstname"
              v-model="form.firstname"
              :error="errors.firstname"
              placeholder="Vorname"
              aria-label="Vorname"
              @update:error="errors.firstname = $event" />
          </InputGroup>
          <InputGroup>
            <InputLabel
              id="name"
              label="Name"
              required
              :error="errors.name" />
            <InputText
              id="name"
              v-model="form.name"
              :error="errors.name"
              placeholder="Nachname"
              aria-label="Nachname"
              @update:error="errors.name = $event" />
          </InputGroup>
          <InputGroup>
            <InputLabel
              id="email"
              label="E-Mail-Adresse"
              required
              :error="errors.email" />
            <InputText
              id="email"
              v-model="form.email"
              :error="errors.email"
              placeholder="E-Mail-Adresse"
              aria-label="E-Mail-Adresse"
              @update:error="errors.email = $event" />
          </InputGroup>
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-y-20">
          <template v-if="authErrors.length > 0">
            <div class="font-muoto-italic text-sm text-flame">
              <div
                v-for="(error, index) in authErrors"
                :key="index">
                {{ error }}
              </div>
            </div>
          </template>
          <InputGroup>
            <InputLabel
              id="current_password"
              label="Passwort ändern" />
            <div class="flex flex-col gap-y-8">
              <InputPassword
                id="current_password"
                v-model="auth.current_password"
                :error="passwordErrors.current_password"
                placeholder="Aktuelles Passwort"
                aria-label="Aktuelles Passwort"
                @update:error="passwordErrors.current_password = $event" />
              <InputPassword
                id="new_password"
                v-model="auth.new_password"
                :error="passwordErrors.new_password"
                placeholder="Neues Passwort"
                aria-label="Neues Passwort"
                @update:error="passwordErrors.new_password = $event" />
              <InputPassword
                id="new_password_confirmation"
                v-model="auth.new_password_confirmation"
                :error="passwordErrors.new_password_confirmation"
                placeholder="Neues Passwort wiederholen"
                aria-label="Passwort wiederholen"
                @update:error="passwordErrors.new_password_confirmation = $event" />
              <AppButton
                variant="secondary"
                type="button"
                label="Passwort ändern"
                @click.stop.prevent="handleAuthSubmit" />
              <div class="pl-8 font-muoto-italic text-sm text-graphite">
                Das Passwort muss mindestens 8 Zeichen umfassen sowie Gross- und Kleinbuchstaben, Zahlen und
                Sonderzeichen enthalten.
              </div>
            </div>
          </InputGroup>
        </div>
      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <AppButton
            label="Speichern"
            :disabled="isSaving" />
        </ButtonGroup>
      </div>
    </form>
  </Slide>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { getUserProfile, updateUserProfile, updatePassword } from '@/services/api/user';
import { useToastStore } from '@/stores/toast';
import { useInfoBox } from '@/composables/useInfoBox';
import { useFormErrors } from '@/composables/useFormErrors';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputPassword from '@/components/forms/Password.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import InfoProfile from '@/views/settings/partials/ProfileInfo.vue';
import Slide from '@/components/slider/Slide.vue';
import { notifyError } from '@/services/api/error';
import AppButton from '@/components/buttons/Button.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false,
  },
});

const isLoading = ref(false);
const isSaving = ref(false);
const { isOpen: isInfoOpen } = useInfoBox({
  active: () => props.isActive,
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

// Profile and password are saved separately, so they keep separate errors
const { errors, setErrors, clearErrors } = useFormErrors(['firstname', 'name', 'email']);
const {
  errors: passwordErrors,
  setErrors: setPasswordErrors,
  clearErrors: clearPasswordErrors,
} = useFormErrors(['current_password', 'new_password', 'new_password_confirmation']);

// All password messages, listed above the fields (they share one label)
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
  } catch (error) {
    notifyError(error, 'Das Profil konnte nicht geladen werden.');
  } finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    const userData = await updateUserProfile(form.value);
    if (userData.email_changed) {
      toast.show('E-Mail-Adresse wurde geändert, wir haben einen neuen Verifizierungslink geschickt.', 'success');
    } else {
      toast.show('Änderungen gespeichert.', 'success');
    }
  } catch (error) {
    setErrors(error);
    notifyError(error, 'Fehler beim Speichern des Profils.');
  } finally {
    isSaving.value = false;
  }
};

const handleAuthSubmit = async () => {
  try {
    authErrors.value = [];
    clearPasswordErrors();
    // Don't set isLoading here - it hides the form and disconnects it from DOM
    await updatePassword(auth.value);
    toast.show('Passwort wurde erfolgreich geändert.', 'success');
    auth.value.current_password = '';
    auth.value.new_password = '';
    auth.value.new_password_confirmation = '';
  } catch (error) {
    setPasswordErrors(error);
    const errorData = error.response?.data?.errors || {};
    authErrors.value = ['current_password', 'new_password', 'new_password_confirmation'].flatMap(
      (field) => errorData[field] || [],
    );

    notifyError(error, 'Es ist ein Fehler aufgetreten.');
  }
};
</script>

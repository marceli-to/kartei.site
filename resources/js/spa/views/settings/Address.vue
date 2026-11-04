<template>
  <Slide :pull="true">
    
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Form elements -->
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Bezeichnung / Firma" id="company" :error="errors.company" />
          <InputText
            v-model="form.company"
            id="company"
            :error="errors.company"
            @update:error="errors.company = $event"
            placeholder="Bezeichnung / Firma"
            aria-label="Bezeichnung / Firma" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Adresszusatz" id="byline" :error="errors.byline" />
          <InputText
            v-model="form.byline"
            id="byline"
            :error="errors.byline"
            @update:error="errors.byline = $event"
            placeholder="Adresszusatz"
            aria-label="Adresszusatz" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Strasse" id="street" required :error="errors.street" />
          <InputText
            v-model="form.street"
            id="street"
            :error="errors.street"
            @update:error="errors.street = $event"
            placeholder="Strasse"
            aria-label="Strasse" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Hausnummer" id="street_number" :error="errors.street_number" />
          <InputText
            v-model="form.street_number"
            id="street_number"
            :error="errors.street_number"
            @update:error="errors.street_number = $event"
            placeholder="Hausnummer"
            aria-label="Hausnummer" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="PLZ" id="zip" required :error="errors.zip" />
          <InputText
            v-model="form.zip"
            id="zip"
            :error="errors.zip"
            @update:error="errors.zip = $event"
            placeholder="PLZ"
            aria-label="PLZ" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Ort" id="city" required :error="errors.city" />
          <InputText
            v-model="form.city"
            id="city"
            :error="errors.city"
            @update:error="errors.city = $event"
            placeholder="Ort"
            aria-label="Ort" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Land" id="country" required :error="errors.country" />
          <InputSelect
            id="country"
            v-model="form.country"
            :options="countries"
            :error="errors.country"
            @update:error="errors.country = $event"
          />
        </InputGroup>
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
import { ref, onMounted } from 'vue';
import { getUserAddress, updateUserAddress } from '@/services/api/user';
import { countries } from '@/config/countries';
import { useToastStore } from '@/components/toast/stores/toast';
import { useFormErrors } from '@/composables/useFormErrors';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputSelect from '@/components/forms/Select.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

const toast = useToastStore();

defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const isLoading = ref(false);
const isSaving = ref(false);

const form = ref({
  company: '',
  byline: '',
  street: '',
  street_number: '',
  zip: '',
  city: '',
  country: 'Schweiz'
});

const { errors, setErrors, clearErrors } = useFormErrors(['company', 'byline', 'street', 'street_number', 'zip', 'city', 'country']);

onMounted(async () => {
  try {
    isLoading.value = true;
    const response = await getUserAddress();
    form.value = { ...form.value, ...response.data };
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
    clearErrors();
    const response = await updateUserAddress(form.value);
    toast.show('Adresse erfolgreich gespeichert.', 'success');
  } 
  catch (error) {
    setErrors(error);
    toast.show('Fehler beim Speichern des Adressen.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

</script>
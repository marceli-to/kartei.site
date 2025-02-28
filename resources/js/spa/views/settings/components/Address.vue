<template>
  <Slide :pull="true">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Form elements -->
      <div class="flex flex-col gap-y-48">

        <div class="flex flex-col gap-y-20">
          <InputGroup>
            <InputLabel label="Bezeichnung / Firma" id="company" />
            <InputText
              v-model="form.company"
              id="company"
              :error="errors.company"
              @update:error="errors.company = $event"
              :placeholder="errors.company ? errors.company : 'Bezeichnung / Firma'"
              aria-label="Bezeichnung / Firma" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Adresszusatz" id="byline" />
            <InputText
              v-model="form.byline"
              id="byline"
              :error="errors.byline"
              @update:error="errors.byline = $event"
              :placeholder="errors.byline ? errors.nbylineame : 'Adresszusatz'"
              aria-label="Adresszusatz" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Strasse" id="street" required />
            <InputText
              v-model="form.street"
              id="street"
              :error="errors.street"
              @update:error="errors.street = $event"
              :placeholder="errors.street ? errors.street : 'Strasse'"
              aria-label="Strasse" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Hausnummer" id="street_number" />
            <InputText
              v-model="form.street_number"
              id="street_number"
              :error="errors.street_number"
              @update:error="errors.street_number = $event"
              :placeholder="errors.street_number ? errors.street_number : 'Hausnummer'"
              aria-label="Hausnummer" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="PLZ" id="zip" required />
            <InputText
              v-model="form.zip"
              id="zip"
              :error="errors.zip"
              @update:error="errors.zip = $event"
              :placeholder="errors.zip ? errors.zip : 'PLZ'"
              aria-label="PLZ" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Ort" id="city" required />
            <InputText
              v-model="form.city"
              id="city"
              :error="errors.city"
              @update:error="errors.city = $event"
              :placeholder="errors.city ? errors.city : 'Ort'"
              aria-label="Ort" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Land" id="country" required />
            <InputSelect
              id="country"
              v-model="form.country"
              :options="countries"
              :error="errors.country"
            />
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
import { ref, onMounted } from 'vue';
import { getUserAddress, updateUserAddress } from '@/services/api/user';
import { countries } from '@/data/countries';
import { useToastStore } from '@/components/toast/stores/toast';
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
  name: '',
  byline: '',
  street: '',
  street_number: '',
  zip: '',
  city: '',
  country: 'Schweiz'
});

const errors = ref({
  company: null,
  byline: null,
  street: null,
  street_number: null,
  zip: null,
  city: null,
  country: null
});

onMounted(async () => {
  try {
    isLoading.value = true;
    const response = await getUserAddress();
    form.value = response.data;
  } 
  catch (error) {
    toast.show('Fehler beim Laden des Adressen.', 'error');
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    const response = await updateUserAddress(form.value);
    toast.show('Adresse erfolgreich gespeichert.', 'success');
  } 
  catch (error) {
    errors.value = {
      company: error.response?.data?.errors?.company?.[0],
      byline: error.response?.data?.errors?.byline?.[0],
      street: error.response?.data?.errors?.street?.[0],
      street_number: error.response?.data?.errors?.street_number?.[0],
      zip: error.response?.data?.errors?.zip?.[0],
      city: error.response?.data?.errors?.city?.[0],
      country: error.response?.data?.errors?.country?.[0],
    }
    toast.show('Fehler beim Speichern des Adressen.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

</script>
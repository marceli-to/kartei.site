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
            <InputLabel label="Bezeichnung / Firma" id="billing_company" />
            <InputText
              v-model="form.billing_company"
              id="billing_company"
              :error="errors.billing_company"
              @update:error="errors.billing_company = $event"
              :placeholder="errors.billing_company ? errors.billing_company : 'Bezeichnung / Firma'"
              aria-label="Bezeichnung / Firma" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Adresszusatz" id="billing_byline" />
            <InputText
              v-model="form.billing_byline"
              id="billing_byline"
              :error="errors.billing_byline"
              @update:error="errors.billing_byline = $event"
              :placeholder="errors.billing_byline ? errors.billing_byline : 'Adresszusatz'"
              aria-label="Adresszusatz" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Strasse" id="billing_street" required />
            <InputText
              v-model="form.billing_street"
              id="billing_street"
              :error="errors.billing_street"
              @update:error="errors.billing_street = $event"
              :placeholder="errors.billing_street ? errors.billing_street : 'Strasse'"
              aria-label="Strasse" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Hausnummer" id="billing_street_number" />
            <InputText
              v-model="form.billing_street_number"
              id="billing_street_number"
              :error="errors.billing_street_number"
              @update:error="errors.billing_street_number = $event"
              :placeholder="errors.billing_street_number ? errors.billing_street_number : 'Hausnummer'"
              aria-label="Hausnummer" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="PLZ" id="billing_zip" required />
            <InputText
              v-model="form.billing_zip"
              id="billing_zip"
              :error="errors.billing_zip"
              @update:error="errors.billing_zip = $event"
              :placeholder="errors.billing_zip ? errors.billing_zip : 'PLZ'"
              aria-label="PLZ" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Ort" id="billing_city" required />
            <InputText
              v-model="form.billing_city"
              id="billing_city"
              :error="errors.billing_city"
              @update:error="errors.billing_city = $event"
              :placeholder="errors.billing_city ? errors.billing_city : 'Ort'"
              aria-label="Ort" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Land" id="billing_country" required />
            <InputSelect
              id="billing_country"
              v-model="form.billing_country"
              :options="countries"
              :error="errors.billing_country"
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
import { getUserBillingAddress, updateUserBillingAddress } from '@/services/api/user';
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
  billing_company: '',
  billing_byline: '',
  billing_street: '',
  billing_street_number: '',
  billing_zip: '',
  billing_city: '',
  billing_country: 'Schweiz'
});

const errors = ref({
  billing_company: null,
  billing_byline: null,
  billing_street: null,
  billing_street_number: null,
  billing_zip: null,
  billing_city: null,
  billing_country: null
});

onMounted(async () => {
  try {
    isLoading.value = true;
    const response = await getUserBillingAddress();
    form.value.billing_company = response.data.company;
    form.value.billing_byline = response.data.byline;
    form.value.billing_street = response.data.street;
    form.value.billing_street_number = response.data.street_number;
    form.value.billing_zip = response.data.zip;
    form.value.billing_city = response.data.city;
    form.value.billing_country = response.data.country;
  } 
  catch (error) {
    toast.show('Fehler beim Laden der Rechnungsadresse.', 'error');
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    const data = {
      company: form.value.billing_company,
      byline: form.value.billing_byline,
      street: form.value.billing_street,
      street_number: form.value.billing_street_number,
      zip: form.value.billing_zip,
      city: form.value.billing_city,
      country: form.value.billing_country
    }
    const response = await updateUserBillingAddress(data);
    toast.show('Rechnungsadresse erfolgreich geändert.', 'success');
  } 
  catch (error) {
    errors.value = {
      billing_company: error.response?.data?.errors?.company?.[0],
      billing_byline: error.response?.data?.errors?.byline?.[0],
      billing_street: error.response?.data?.errors?.street?.[0],
      billing_street_number: error.response?.data?.errors?.street_number?.[0],
      billing_zip: error.response?.data?.errors?.zip?.[0],
      billing_city: error.response?.data?.errors?.city?.[0],
      billing_country: error.response?.data?.errors?.country?.[0],
    }
    toast.show('Fehler beim Speichern der Rechnungsadresse.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

</script>
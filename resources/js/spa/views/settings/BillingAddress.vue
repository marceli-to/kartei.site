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
            <InputLabel label="Bezeichnung / Firma" id="billing_company" :error="errors.billing_company" />
            <InputText
              v-model="form.billing_company"
              id="billing_company"
              :error="errors.billing_company"
              @update:error="errors.billing_company = $event"
              placeholder="Bezeichnung / Firma"
              aria-label="Bezeichnung / Firma" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Adresszusatz" id="billing_byline" :error="errors.billing_byline" />
            <InputText
              v-model="form.billing_byline"
              id="billing_byline"
              :error="errors.billing_byline"
              @update:error="errors.billing_byline = $event"
              placeholder="Adresszusatz"
              aria-label="Adresszusatz" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Strasse" id="billing_street" required :error="errors.billing_street" />
            <InputText
              v-model="form.billing_street"
              id="billing_street"
              :error="errors.billing_street"
              @update:error="errors.billing_street = $event"
              placeholder="Strasse"
              aria-label="Strasse" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Hausnummer" id="billing_street_number" :error="errors.billing_street_number" />
            <InputText
              v-model="form.billing_street_number"
              id="billing_street_number"
              :error="errors.billing_street_number"
              @update:error="errors.billing_street_number = $event"
              placeholder="Hausnummer"
              aria-label="Hausnummer" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="PLZ" id="billing_zip" required :error="errors.billing_zip" />
            <InputText
              v-model="form.billing_zip"
              id="billing_zip"
              :error="errors.billing_zip"
              @update:error="errors.billing_zip = $event"
              placeholder="PLZ"
              aria-label="PLZ" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Ort" id="billing_city" required :error="errors.billing_city" />
            <InputText
              v-model="form.billing_city"
              id="billing_city"
              :error="errors.billing_city"
              @update:error="errors.billing_city = $event"
              placeholder="Ort"
              aria-label="Ort" />
          </InputGroup>
          <InputGroup>
            <InputLabel label="Land" id="billing_country" required :error="errors.billing_country" />
            <InputSelect
              id="billing_country"
              v-model="form.billing_country"
              :options="countries"
              :error="errors.billing_country"
              @update:error="errors.billing_country = $event"
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
  billing_company: '',
  billing_byline: '',
  billing_street: '',
  billing_street_number: '',
  billing_zip: '',
  billing_city: '',
  billing_country: 'Schweiz'
});

const { errors, setErrors, clearErrors } = useFormErrors({
  billing_company: 'company',
  billing_byline: 'byline',
  billing_street: 'street',
  billing_street_number: 'street_number',
  billing_zip: 'zip',
  billing_city: 'city',
  billing_country: 'country',
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
    setErrors(error);
    toast.show('Fehler beim Speichern der Rechnungsadresse.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

</script>
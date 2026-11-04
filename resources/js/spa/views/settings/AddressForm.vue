<template>
  <Slide :pull="true">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <AddressFields :form="form" :errors="errors" :idPrefix="config.idPrefix" />

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary label="Speichern" :loading="isSaving" />
        </ButtonGroup>
      </div>

    </form>
  </Slide>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { getUserAddress, updateUserAddress, getUserBillingAddress, updateUserBillingAddress } from '@/services/api/user';
import { notifyError } from '@/services/api/error';
import { useToastStore } from '@/stores/toast';
import { useFormErrors } from '@/composables/useFormErrors';
import AddressFields from '@/components/forms/AddressFields.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

// The account's address and its billing address: same fields, other endpoints
const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'address',
    validator: (value) => ['address', 'billing'].includes(value)
  }
});

const CONFIG = {
  address: {
    load: getUserAddress,
    save: updateUserAddress,
    idPrefix: '',
    loadFailed: 'Die Adresse konnte nicht geladen werden.',
    saved: 'Adresse erfolgreich gespeichert.',
    saveFailed: 'Fehler beim Speichern der Adresse.',
  },
  billing: {
    load: getUserBillingAddress,
    save: updateUserBillingAddress,
    idPrefix: 'billing_',
    loadFailed: 'Die Rechnungsadresse konnte nicht geladen werden.',
    saved: 'Rechnungsadresse erfolgreich gespeichert.',
    saveFailed: 'Fehler beim Speichern der Rechnungsadresse.',
  },
};
const config = CONFIG[props.type];

const toast = useToastStore();
const isLoading = ref(true);
const isSaving = ref(false);

const FIELDS = ['company', 'byline', 'street', 'street_number', 'zip', 'city', 'country'];
const form = ref({
  company: '',
  byline: '',
  street: '',
  street_number: '',
  zip: '',
  city: '',
  country: 'Schweiz'
});

const { errors, setErrors, clearErrors } = useFormErrors(FIELDS);

onMounted(async () => {
  try {
    const response = await config.load();
    // Only the address fields; missing values keep the defaults
    for (const key of FIELDS) {
      if (response.data?.[key] != null) form.value[key] = response.data[key];
    }
  } 
  catch (error) {
    notifyError(error, config.loadFailed);
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    await config.save(form.value);
    toast.show(config.saved, 'success');
  } 
  catch (error) {
    setErrors(error);
    notifyError(error, config.saveFailed);
  }
  finally {
    isSaving.value = false;
  }
};
</script>

<template>
  <Slide :pull="true">
    <form
      v-if="!isLoading"
      class="flex h-full w-full flex-col justify-between"
      @submit.prevent="handleSubmit">
      <!-- Info box -->
      <template v-if="isInfoOpen">
        <InfoBox class="pb-24">
          <PlanInfo />
        </InfoBox>
      </template>

      <!-- Form elements -->
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel
            id="subscription"
            label="Lizenz"
            :error="errors.subscription"
            required />
          <InputRadioGroup
            v-model="form.subscription"
            legend="Lizenz"
            :error="errors.subscription"
            name="subscription"
            :options="subscriptions"
            variant="reduced" />
        </InputGroup>
        <InputGroup>
          <InputLabel
            id="payment_interval"
            label="Abrechnung"
            :error="errors.payment_interval"
            required />
          <InputRadioGroup
            v-model="form.payment_interval"
            legend="Abrechnung"
            :error="errors.payment_interval"
            name="payment_interval"
            :options="paymentIntervals"
            variant="reduced" />
        </InputGroup>
        <InputGroup>
          <InputLabel
            id="payment_method"
            label="Zahlungsart"
            :error="errors.payment_method"
            required />
          <InputRadioGroup
            v-model="form.payment_method"
            legend="Zahlungsart"
            :error="errors.payment_method"
            name="payment_method"
            :options="[
              {
                label: 'Kreditkarte',
                value: 'card',
              },
            ]"
            variant="reduced" />
        </InputGroup>
      </div>
      <div v-if="isDev">
        <div class="border border-flame p-8 text-center font-muoto-medium text-flame">
          To do: implement payment with stripe
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
import { ref, watch, onMounted } from 'vue';
import { getSubscriptionPlans } from '@/services/api/subscription';
import { getUserSubscription, updateUserSubscription } from '@/services/api/subscription';
import { paymentIntervals } from '@/config/paymentIntervals';
import { useToastStore } from '@/stores/toast';
import { useInfoBox } from '@/composables/useInfoBox';
import { useFormErrors } from '@/composables/useFormErrors';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputRadioGroup from '@/components/forms/RadioGroup.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import PlanInfo from '@/views/settings/partials/PlanInfo.vue';
import Slide from '@/components/slider/Slide.vue';
import { notifyError } from '@/services/api/error';
import AppButton from '@/components/buttons/Button.vue';

// Dev-only reminder in the template; never shown in production builds
const isDev = import.meta.env.DEV;

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false,
  },
});

const isLoading = ref(false);
const isSaving = ref(false);

const subscriptions = ref([]);
const hasSubscription = ref(false);

// Opens by itself while there is no subscription yet
const { isOpen: isInfoOpen } = useInfoBox({
  active: () => props.isActive,
  auto: () => !isLoading.value && !hasSubscription.value,
});

const form = ref({
  subscription: '',
  payment_interval: '',
  payment_method: 'card',
});

const { errors, setErrors, clearErrors } = useFormErrors(['subscription', 'payment_interval', 'payment_method']);

// An error clears as soon as its field changes
for (const field of Object.keys(errors.value)) {
  watch(
    () => form.value[field],
    () => (errors.value[field] = null),
  );
}

onMounted(async () => {
  try {
    isLoading.value = true;
    const response = await getSubscriptionPlans();
    const userSubscription = await getUserSubscription();

    subscriptions.value = response.data.map((subscription) => ({
      label: subscription.title,
      value: subscription.uuid,
    }));

    // Fix: check .data instead of the whole object
    hasSubscription.value = !!userSubscription.data;

    if (userSubscription.data) {
      form.value.payment_interval = userSubscription.data.payment_interval;
      form.value.payment_method = userSubscription.data.payment_method;
      form.value.subscription = userSubscription.data.plan.uuid;
    }
  } catch (error) {
    notifyError(error, 'Das Abonnement konnte nicht geladen werden.');
  } finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    clearErrors();
    await updateUserSubscription(form.value);
    hasSubscription.value = true;
    toast.show('Abonnement erfolgreich gespeichert.', 'success');
  } catch (error) {
    setErrors(error);
    notifyError(error, 'Fehler beim Ändern des Abonnements.');
  } finally {
    isSaving.value = false;
  }
};
</script>

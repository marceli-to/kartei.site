<template>
  <Slide :pull="true">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Info box -->
      <template v-if="(!hasSubscription && isActive) || infoBox.isActive">
        <InfoBox class="pb-24">
          <InfoSubscription />
        </InfoBox>
      </template>

      <!-- Form elements -->
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Lizenz" id="subscription" required />
          <InputRadioGroup
            v-model="form.subscription"
            name="subscription"
            :options="subscriptions"
            variant="reduced" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Abrechnung" id="payment_interval" required />
          <InputRadioGroup
            v-model="form.payment_interval"
            name="payment_interval"
            :options="payment_intervals"
            variant="reduced" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Zahlungsart" id="payment_method" required />
          <InputRadioGroup
            v-model="form.payment_method"
            name="payment_method"
            :options="[{
              'label': 'Kreditkarte',
              'value': 'card'
            }]"
            variant="reduced" />
        </InputGroup>
      </div>
      <div>
        <div class="font-muoto-medium text-flame border border-flame p-8 text-center">
          To do: implement payment with stripe
        </div>
      </div>
      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary label="Speichern" />
        </ButtonGroup>
      </div>
    </form>
  </Slide>
</template>
<script setup>
import { ref, toRef, onMounted, watch } from 'vue';
import { getSubscriptionPlans } from '@/services/api/subscription';
import { getUserSubscription, updateUserSubscription } from '@/services/api/user';
import { payment_intervals } from '@/data/payment_intervals';
import { useToastStore } from '@/components/toast/stores/toast';
import { useInfoBox } from '@/components/infobox/composables/useInfoBox';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputRadioGroup from '@/components/forms/RadioGroup.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import InfoSubscription from '@/views/settings/components/SubscriptionInfo.vue';
import Slide from '@/components/slider/Slide.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const isLoading = ref(false);
const subscriptions = ref([]);
const hasSubscription = ref(false);

const infoBox = useInfoBox({
  isActive: toRef(props, 'isActive'),
  condition: hasSubscription
});

const form = ref({
  subscription: '',
  payment_interval: '',
  payment_method: 'card'
});

onMounted(async () => {
  try {
    isLoading.value = true;
    const response = await getSubscriptionPlans();
    const userSubscription = await getUserSubscription();
    
    // Map the subscription plans to an array of labels and values
    subscriptions.value = response.data.map((subscription) => ({
      label: subscription.title,
      value: subscription.uuid
    }));

    // Set subscription status
    hasSubscription.value = !!userSubscription;
    
    if (userSubscription) {
      form.value.payment_interval = userSubscription.data.payment_interval;
      form.value.payment_method = userSubscription.data.payment_method;
      form.value.subscription = userSubscription.data.plan.uuid;
    }
  } 
  catch (error) {
    toast.show('Fehler beim Laden der Abonnements.', 'error');
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isLoading.value = true;
    await updateUserSubscription(form.value);
    hasSubscription.value = true;
    toast.show('Abonnement erfolgreich gespeichert.', 'success');
  } 
  catch (error) {
    toast.show('Fehler beim Ändern des Abonnements.', 'error');
  }
  finally {
    isLoading.value = false;
  }
}
</script>
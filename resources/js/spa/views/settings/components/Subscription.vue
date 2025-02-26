<template>
  <form 
    @submit.prevent="handleSubmit"
    class="px-8 relative w-full flex flex-col justify-between pb-16 h-full -top-24"
    v-if="!isLoading">

    <!-- Subscription -->
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
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { getSubscriptionPlans } from '@/services/api/subscription';
import { getUserSubscription, updateUserSubscription } from '@/services/api/user';
import InputGroup from '@/components/fields/Group.vue';
import InputLabel from '@/components/fields/Label.vue';
import InputRadioGroup from '@/components/fields/RadioGroup.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import { payment_intervals } from '@/data/payment_intervals';
import { useToastStore } from '@/store/toast';
const toast = useToastStore();

defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const isLoading = ref(false);

const subscriptions = ref([]);

const form = ref({
  subscription: '',
  payment_interval: 'monthly',
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

    // Set the current subscription and payment settings if it exists
    if (userSubscription) {
      form.value.payment_interval = userSubscription.data.payment_interval;
      form.value.payment_method = userSubscription.data.payment_method;
      form.value.subscription = userSubscription.data.plan.uuid;
    }
    else {
      if (response.data && response.data.length > 0) {
        form.value.subscription = response.data[0].uuid;
      }
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
    toast.show('Abonnement erfolgreich geändert.', 'success');
  } 
  catch (error) {
    toast.show('Fehler beim Ändern des Abonnements.', 'error');
  }
  finally {
    isLoading.value = false;
  }
}
</script>
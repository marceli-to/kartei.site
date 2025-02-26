<template>
  <form 
    @submit.prevent="handleSubmit"
    class="px-8 relative w-full flex flex-col justify-between pb-16 h-full -top-24"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputLabel label="Lizenz" id="subscription" required />
        <InputRadioGroup
          v-model="form.subscription"
          name="subscription"
          :options="subscriptions"
          variant="reduced" />
      </InputGroup>
    </div>
  </form>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { getSubscriptionPlans } from '@/services/api/subscription';
import InputGroup from '@/components/fields/Group.vue';
import InputLabel from '@/components/fields/Label.vue';
import InputRadioGroup from '@/components/fields/RadioGroup.vue';
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
  subscription: ''
});

onMounted(async () => {
  try {
    isLoading.value = true;
    const response = await getSubscriptionPlans();

    
    // Here's the issue: You're overwriting subscriptions.value with a mapping
    // of the same array, which would be empty at that point
    subscriptions.value = response.data.map((subscription) => ({
      label: subscription.title,
      value: subscription.uuid
    }));

    // Only set the default value if there are plans available
    if (response.data && response.data.length > 0) {
      form.value.subscription = response.data[0].uuid;
    }
  } 
  catch (error) {
    console.error(error);
    toast.show('Fehler beim Laden der Abonnements.', 'error');
  }
  finally {
    isLoading.value = false;
  }
});
</script>
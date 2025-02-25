<template>
  <SlidingSections 
    :sections="sections" 
    :initialActiveIndex="0"
    @section-change="handleSectionChange">
    
    <template #section-0>
      <ProfileComponent />
    </template>
    
    <template #section-1>
      <AddressComponent />
    </template>

    <template #section-2>
      <InvoiceAddressComponent />
    </template>

    <template #section-3>
      <SubscriptionComponent />
    </template>

  </SlidingSections>
</template>

<script setup>
import { ref } from 'vue';
import SlidingSections from '@/components/SlidingSections.vue';
import ProfileComponent from '@/views/settings/components/Profile.vue';
import AddressComponent from '@/views/settings/components/Address.vue';
import InvoiceAddressComponent from '@/views/settings/components/InvoiceAddress.vue';
import SubscriptionComponent from '@/views/settings/components/Subscription.vue';

const sections = ref([
  { name: "Profil", width: 25, class: "w-3/12" },
  { name: "Adresse", width: 25, class: "w-3/12" },
  { name: "Rechnungsadresse", width: 25, class: "w-3/12" },
  { name: "Abonnement", width: 25, class: "w-3/12" },
]);

function handleSectionChange(newIndex) {
  console.log(`Active section changed to: ${sections.value[newIndex].name}`);
}
</script>
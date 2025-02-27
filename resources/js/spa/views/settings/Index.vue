<template>
  <SlidingSections 
    :sections="sections" 
    :initialActiveIndex="0"
    @section-change="handleSectionChange">
    
    <template #section-0>
      <ProfileComponent :isActive="activeIndex === 0" />
    </template>
    
    <template #section-1>
      <AddressComponent :isActive="activeIndex === 1" />
    </template>

    <template #section-2>
      <BillingAddressComponent :isActive="activeIndex === 2" />
    </template>

    <template #section-3>
      <SubscriptionComponent :isActive="activeIndex === 3" />
    </template>

    <template #section-4>
      <AccountDeleteComponent :isActive="activeIndex === 4" />
    </template>

  </SlidingSections>
</template>

<script setup>
import { ref } from 'vue';
import SlidingSections from '@/components/layout/SlidingSections.vue';
import ProfileComponent from '@/views/settings/components/Profile.vue';
import AddressComponent from '@/views/settings/components/Address.vue';
import BillingAddressComponent from '@/views/settings/components/BillingAddress.vue';
import SubscriptionComponent from '@/views/settings/components/Subscription.vue';
import AccountDeleteComponent from '@/views/settings/components/AccountDelete.vue';

const sections = ref([
  { name: "Profil", width: 25, class: "w-3/12" },
  { name: "Adresse", width: 25, class: "w-3/12" },
  { name: "Rechnungsadresse", width: 25, class: "w-3/12" },
  { name: "Abonnement", width: 25, class: "w-3/12" },
  { name: "Löschen", width: 25, class: "w-3/12" }
]);

const activeIndex = ref(0);

function handleSectionChange(newIndex) {
  console.log(`Active section changed to: ${sections.value[newIndex].name}`);
  activeIndex.value = newIndex;
}
</script>
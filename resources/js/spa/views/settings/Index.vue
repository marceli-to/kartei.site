<template>
  <SlidingSections 
    :sections="availableSections" 
    :initialActiveIndex="0"
    @section-change="handleSectionChange">
  </SlidingSections>
</template>

<script setup>
import { ref, computed, markRaw } from 'vue';
import SlidingSections from '@/components/layout/SlidingSections.vue';
import ProfileComponent from '@/views/settings/components/Profile.vue';
import AddressComponent from '@/views/settings/components/Address.vue';
import BillingAddressComponent from '@/views/settings/components/BillingAddress.vue';
import SubscriptionComponent from '@/views/settings/components/Subscription.vue';
import ThemeComponent from '@/views/settings/components/Theme.vue';
import AccountDeleteComponent from '@/views/settings/components/AccountDelete.vue';
import { useUserStore } from '@/stores/user';

const userStore = useUserStore();
const activeIndex = ref(0);

// Define all possible sections with their permissions and components
const allSections = [
  { 
    id: 'profile', 
    name: "Profil", 
    width: 25, 
    class: "w-3/12", 
    permission: 'edit.profile',
    component: markRaw(ProfileComponent)
  },
  { 
    id: 'address', 
    name: "Adresse", 
    width: 25, 
    class: "w-3/12", 
    permission: 'edit.address',
    component: markRaw(AddressComponent)
  },
  { 
    id: 'billingAddress', 
    name: "Rechnungsadresse", 
    width: 25, 
    class: "w-3/12", 
    permission: 'edit.billing-address',
    component: markRaw(BillingAddressComponent)
  },
  { 
    id: 'subscription', 
    name: "Abonnement", 
    width: 25, 
    class: "w-3/12", 
    permission: 'edit.subscription',
    component: markRaw(SubscriptionComponent)
  },
  { 
    id: 'theme', 
    name: "Erscheinungsbild", 
    width: 25, 
    class: "w-3/12", 
    permission: 'use.themes',
    component: markRaw(ThemeComponent)
  },
  { 
    id: 'deleteAccount', 
    name: "Löschen", 
    width: 25, 
    class: "w-3/12", 
    permission: 'delete.account',
    component: markRaw(AccountDeleteComponent)
  }
];

// Filter sections based on permissions
const availableSections = computed(() => {
  return allSections.filter(section => userStore.can(section.permission));
});

function handleSectionChange(newIndex) {
  if (newIndex >= 0 && newIndex < availableSections.value.length) {
    console.log(`Active section changed to: ${availableSections.value[newIndex].name}`);
    activeIndex.value = newIndex;
  }
}
</script>
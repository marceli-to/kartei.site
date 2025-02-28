<template>
  <SliderContainer 
    :slides="slides" 
    :initialActiveIndex="0"
    @slide-change="handleSlideChange">
  </SliderContainer>
</template>

<script setup>
import { ref, computed, markRaw } from 'vue';
import { useUserStore } from '@/stores/user';
import SliderContainer from '@/components/slider/Container.vue';
import ProfileComponent from '@/views/settings/Profile.vue';
import AddressComponent from '@/views/settings/Address.vue';
import BillingAddressComponent from '@/views/settings/BillingAddress.vue';
import SubscriptionComponent from '@/views/settings/Subscription.vue';
import ThemeComponent from '@/views/settings/Theme.vue';
import AccountDeleteComponent from '@/views/settings/AccountDelete.vue';

const userStore = useUserStore();
const activeIndex = ref(0);

// Define all possible slides with their permissions and components
const allSlides = [
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
const slides = computed(() => {
  return allSlides.filter(section => userStore.can(section.permission));
});

function handleSlideChange(newIndex) {
  if (newIndex >= 0 && newIndex < slides.value.length) {
    // console.log(`Active slide changed to: ${slides.value[newIndex].name}`);
    activeIndex.value = newIndex;
  }
}
</script>
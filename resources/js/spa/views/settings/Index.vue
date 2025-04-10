<template>
  <SliderContainer 
    :slides="slides" 
    :initialActiveIndex="0"
    @slide-change="handleSlideChange">
  </SliderContainer>
</template>

<script setup>
import { markRaw } from 'vue';
import { usePageTitle } from '@/composables/usePageTitle';
import { useSlider } from '@/components/slider/composable/useSlider';
import SliderContainer from '@/components/slider/Container.vue';
import ProfileComponent from '@/views/settings/Profile.vue';
import AddressComponent from '@/views/settings/Address.vue';
import BillingAddressComponent from '@/views/settings/BillingAddress.vue';
import CompanyComponent from '@/views/settings/Company.vue';
import UserComponent from '@/views/users/Index.vue';
import SubscriptionComponent from '@/views/settings/Subscription.vue';
import ThemeComponent from '@/views/settings/Theme.vue';
import AccountDeleteComponent from '@/views/settings/AccountDelete.vue';

const { setTitle } = usePageTitle();
setTitle('Einstellungen');

// Define all possible slides with their permissions and components
const allSlides = [
  { 
    id: 'profile', 
    name: "Profil", 
    class: "w-3/12 min-w-[300px]", 
    component: markRaw(ProfileComponent)
  },
  { 
    id: 'address', 
    name: "Adresse", 
    class: "w-3/12 min-w-[300px]", 
    permission: 'edit.address',
    component: markRaw(AddressComponent)
  },
  { 
    id: 'billingAddress', 
    name: "Rechnungsadresse", 
    class: "w-3/12 min-w-[300px]", 
    permission: 'edit.address',
    component: markRaw(BillingAddressComponent)
  },
  { 
    id: 'subscription', 
    name: "Abonnement", 
    class: "w-3/12 min-w-[300px]", 
    permission: 'edit.subscription',
    component: markRaw(SubscriptionComponent)
  },
  { 
    id: 'company', 
    name: "Kundinnen/Kunden", 
    class: "w-3/12 min-w-[300px]", 
    permission: 'edit.clients',
    component: markRaw(CompanyComponent)
  },
  { 
    id: 'user', 
    name: "Benutzer", 
    class: "w-3/12", 
    permission: 'edit.users',
    component: markRaw(UserComponent)
  },
  { 
    id: 'theme', 
    name: "Erscheinungsbild", 
    class: "w-3/12 min-w-[300px]", 
    component: markRaw(ThemeComponent)
  },
  { 
    id: 'deleteAccount', 
    name: "Löschen", 
    class: "w-3/12 min-w-[300px]", 
    component: markRaw(AccountDeleteComponent)
  }
];

const { slides, handleSlideChange } = useSlider(allSlides);
</script>
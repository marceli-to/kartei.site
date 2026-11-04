<template>
  <Slider :slides="slides" />
</template>

<script setup>
import { markRaw } from 'vue';
import { usePageTitle } from '@/composables/usePageTitle';
import Slider from '@/components/slider/Slider.vue';
import ProfileComponent from '@/views/settings/Profile.vue';
import AddressFormComponent from '@/views/settings/AddressForm.vue';
import CompanyComponent from '@/views/settings/Company.vue';
import UserComponent from '@/views/users/Index.vue';
import SubscriptionComponent from '@/views/settings/Subscription.vue';
import ThemeComponent from '@/views/settings/Theme.vue';
import AccountDeleteComponent from '@/views/settings/AccountDelete.vue';

const { setTitle } = usePageTitle();
setTitle('Einstellungen');

// Define all possible slides with their permissions and components
const slides = [
  { 
    id: 'profile', 
    name: "Profil", 
    class: "w-3/12 min-w-3/12", 
    component: markRaw(ProfileComponent)
  },
  { 
    id: 'address', 
    name: "Adresse", 
    class: "w-3/12 min-w-3/12", 
    permission: 'edit.address',
    component: markRaw(AddressFormComponent),
    props: { type: 'address' }
  },
  { 
    id: 'billingAddress', 
    name: "Rechnungsadresse", 
    class: "w-3/12 min-w-3/12", 
    permission: 'edit.address',
    component: markRaw(AddressFormComponent),
    props: { type: 'billing' }
  },
  { 
    id: 'subscription', 
    name: "Abonnement", 
    class: "w-3/12 min-w-3/12", 
    permission: 'edit.subscription',
    component: markRaw(SubscriptionComponent)
  },
  { 
    id: 'company', 
    name: "Kundinnen/Kunden", 
    class: "w-3/12 min-w-3/12", 
    permission: 'edit.clients',
    component: markRaw(CompanyComponent)
  },
  { 
    id: 'user', 
    name: "Benutzer", 
    class: "w-3/12 min-w-3/12", 
    permission: 'edit.users',
    component: markRaw(UserComponent)
  },
  { 
    id: 'theme', 
    name: "Erscheinungsbild", 
    class: "w-3/12 min-w-3/12", 
    component: markRaw(ThemeComponent)
  },
  { 
    id: 'deleteAccount', 
    name: "Löschen", 
    class: "w-3/12 min-w-3/12", 
    component: markRaw(AccountDeleteComponent)
  }
];
</script>
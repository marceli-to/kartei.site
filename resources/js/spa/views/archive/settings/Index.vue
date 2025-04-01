<template>
  <SliderContainer 
    :slides="slides" 
    :initialActiveIndex="0"
    :navigationVariant="'box'"
    @slide-change="handleSlideChange">
    <template #navigationTitle>
      <h2 class="flex justify-between items-center mb-32 mr-8">
        <span>Voreinstellungen</span>
        <router-link :to="{ name: 'archives' }">
          <IconCross variant="small" />
        </router-link>
      </h2>
    </template>
  </SliderContainer>
</template>

<script setup>
import { markRaw } from 'vue';
import { usePageTitle } from '@/composables/userPageTitle';
import { useSlider } from '@/components/slider/composable/useSlider';
import SliderContainer from '@/components/slider/Container.vue';
import BasicInformationComponent from '@/views/archive/settings/BasicInformation.vue';
import UserComponent from '@/views/settings/User.vue';
import AccountDeleteComponent from '@/views/settings/AccountDelete.vue';
import IconCross from '@/components/icons/Cross.vue';

const { setTitle } = usePageTitle();
setTitle('Einstellungen');

const allSlides = [
  { 
    id: 'basic-information', 
    name: "Basisinformationen", 
    width: 25, 
    class: "w-3/12", 
    component: markRaw(BasicInformationComponent),
    props: {
      archive: 'f1598139-024e-449d-8924-a0905b104ac5'
    }
  },
  // { 
  //   id: 'user', 
  //   name: "Benutzer", 
  //   width: 25, 
  //   class: "w-3/12", 
  //   permission: 'edit.users',
  //   component: markRaw(UserComponent)
  // },
  { 
    id: 'deleteAccount', 
    name: "Löschen", 
    width: 25, 
    class: "w-3/12", 
    component: markRaw(AccountDeleteComponent)
  }
];

const { slides, handleSlideChange } = useSlider(allSlides);
</script>
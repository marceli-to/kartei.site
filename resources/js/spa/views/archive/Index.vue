<template>
  <SliderContainer 
    :slides="slides" 
    :initialActiveIndex="0"
    :navigationVariant="'box'"
    @slide-change="handleSlideChange">
    <template #navigationTitle>
      <h2 class="flex justify-between items-center mb-38 mr-8">
        <span>Voreinstellungen</span>
        <router-link :to="{ name: 'archives' }">
          <IconCross variant="small" />
        </router-link>
      </h2>
    </template>
  </SliderContainer>
</template>

<script setup>
import { markRaw, watch, ref } from 'vue';
import { usePageTitle } from '@/composables/usePageTitle';
import { useSlider } from '@/components/slider/composable/useSlider';
import { useRoute } from 'vue-router';
import SliderContainer from '@/components/slider/Container.vue';
import BasicInformationComponent from '@/views/archive/BasicInformation.vue';
import TagsComponent from '@/views/archive/Tags.vue';
import UserComponent from '@/views/users/Index.vue';
import DeleteComponent from '@/views/archive/Delete.vue';
import IconCross from '@/components/icons/Cross.vue';

const route = useRoute();
const { setTitle } = usePageTitle();
setTitle('Einstellungen');

// Create initial slides configuration
const createSlides = () => [
  { 
    id: 'basic-information', 
    name: "Basisinformationen", 
    width: 25, 
    class: "w-3/12", 
    component: markRaw(BasicInformationComponent),
  },
  { 
    id: 'tags', 
    name: "Tags", 
    width: 25, 
    class: "w-3/12", 
    permission: 'edit.tags',
    component: markRaw(TagsComponent),
    disabled: !route.params.uuid
  },
  { 
    id: 'user', 
    name: "Benutzer", 
    width: 25, 
    class: "w-3/12", 
    permission: 'edit.users',
    component: markRaw(UserComponent),
    disabled: !route.params.uuid
  },
  { 
    id: 'delete', 
    name: "Löschen", 
    width: 25, 
    class: "w-3/12", 
    component: markRaw(DeleteComponent),
    disabled: !route.params.uuid
  }
];

// Initialize slider with slides
const initialSlides = createSlides();
const { slides, handleSlideChange, updateSlides } = useSlider(initialSlides);

// Watch for changes in route uuid parameter and update slides accordingly
watch(() => route.params.uuid, () => {
  const updatedSlides = createSlides();
  updateSlides(updatedSlides);
}, { immediate: true });
</script>
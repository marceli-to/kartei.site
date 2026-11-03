<template>
  <SliderContainer 
    :slides="slides" 
    :initialActiveIndex="0"
    :navigationVariant="'box'"
    @slide-change="handleSlideChange">
    <template #navigationTitle>
      <SliderNavigationHeader :title="'Voreinstellungen'" :uuid="uuid" :archive="archive" />
    </template>
  </SliderContainer>
</template>

<script setup>
import { markRaw, watch, ref, computed } from 'vue';
import { usePageTitle } from '@/composables/usePageTitle';
import { useSlider } from '@/components/slider/composable/useSlider';
import { useRoute } from 'vue-router';
import { getArchive } from '@/services/api/archive';
import { useUserStore } from '@/stores/user';

import SliderContainer from '@/components/slider/Container.vue';
import SliderNavigationHeader from '@/components/slider/NavigationHeader.vue';

import BasicInformationComponent from '@/views/archive/BasicInformation.vue';
import CategoryComponent from '@/views/archive/Category.vue';
import CardComponent from '@/views/archive/Card.vue';
import TagsComponent from '@/views/archive/Tags.vue';
import UserComponent from '@/views/users/Index.vue';
import DeleteComponent from '@/views/archive/Delete.vue';

const route = useRoute();
const uuid = computed(() => route.params.uuid || null);

const { setTitle } = usePageTitle();
const userStore = useUserStore();

// Per-archive check; without uuid the page creates a new archive
const allows = (ability) => () =>
  uuid.value ? userStore.canArchive(uuid.value, ability) : userStore.can('create.archive');

const archive = ref(null);
const isLoading = ref(false);

// Create initial slides configuration
const createSlides = () => [
  { 
    id: 'basic-information', 
    name: "Basisinformationen", 
    class: "w-3/12 min-w-3/12", 
    permission: allows('update'),
    component: markRaw(BasicInformationComponent),
  },
  { 
    id: 'structure', 
    name: "Struktur", 
    class: "w-6/12 min-w-6/12", 
    permission: allows('manageStructure'),
    component: markRaw(CategoryComponent),
    disabled: !uuid.value
  },
  { 
    id: 'card', 
    name: "Kartenvorlage", 
    class: "w-3/12 min-w-3/12", 
    permission: allows('manageTemplate'),
    component: markRaw(CardComponent),
    disabled: !uuid.value
  },
  { 
    id: 'tags', 
    name: "Tags", 
    class: "w-3/12 min-w-3/12", 
    permission: allows('manageTags'),
    component: markRaw(TagsComponent),
    disabled: !uuid.value
  },
  { 
    id: 'user', 
    name: "Benutzer", 
    class: "w-3/12 min-w-3/12", 
    permission: allows('manageUsers'),
    component: markRaw(UserComponent),
    disabled: !uuid.value
  },
  { 
    id: 'delete', 
    name: "Löschen", 
    class: "w-3/12 min-w-3/12", 
    permission: allows('delete'),
    component: markRaw(DeleteComponent),
    disabled: !uuid.value
  }
];

// Initialize slider with slides
const initialSlides = createSlides();
const { slides, handleSlideChange, updateSlides } = useSlider(initialSlides);

const fetchArchive = async () => {
  try {
    isLoading.value = true;
    const response = await getArchive(uuid.value);
    archive.value = response;
    setTitle(archive.value.name);
  }
  catch (error) {
    console.error(error);
  } 
  finally {
    isLoading.value = false;
  }
};

// Watch for changes in route uuid parameter and update slides accordingly
watch(uuid, () => {
  const updatedSlides = createSlides();
  updateSlides(updatedSlides);
  if (uuid.value) {
    fetchArchive();
  }
  else {
    setTitle('Einstellungen');
  }
}, { immediate: true });

</script>
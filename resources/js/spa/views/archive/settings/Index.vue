<template>
  <SliderContainer 
    :slides="slides" 
    :initialActiveIndex="0"
    :navigationVariant="'box'"
    @slide-change="handleSlideChange">
    <template #navigationTitle>
      <h2 class="flex justify-between items-center mb-32 mr-8">
        <span>Voreinstellungen</span>
        <router-link :to="{ name: 'archives' }" @click="handleClose">
          <IconCross variant="small" />
        </router-link>
      </h2>
    </template>
  </SliderContainer>
</template>

<script setup>
import { markRaw, watch, computed } from 'vue';
import { usePageTitle } from '@/composables/userPageTitle';
import { useSlider } from '@/components/slider/composable/useSlider';
import { useArchiveStore } from '@/stores/archive';
import SliderContainer from '@/components/slider/Container.vue';
import BasicInformationComponent from '@/views/archive/settings/BasicInformation.vue';
import TagsComponent from '@/views/archive/settings/Tags.vue';
import AccountDeleteComponent from '@/views/settings/AccountDelete.vue';
import IconCross from '@/components/icons/Cross.vue';

const archiveStore = useArchiveStore();

const { setTitle } = usePageTitle();
setTitle('Einstellungen');

// Create initial slides configuration
const createSlides = (archiveId) => [
  { 
    id: 'basic-information', 
    name: "Basisinformationen", 
    width: 25, 
    class: "w-3/12", 
    component: markRaw(BasicInformationComponent),
    props: {
      archive: archiveId
    }
  },
  { 
    id: 'tags', 
    name: "Tags", 
    width: 25, 
    class: "w-3/12", 
    permission: 'edit.tags',
    component: markRaw(TagsComponent),
    props: {
      archive: archiveId
    },
    disabled: !archiveId
  },
  { 
    id: 'deleteAccount', 
    name: "Löschen", 
    width: 25, 
    class: "w-3/12", 
    component: markRaw(AccountDeleteComponent),
    props: {
      archive: archiveId
    },
    disabled: !archiveId
  }
];

// Initialize slider with current archive ID
const initialSlides = createSlides(archiveStore.currentArchiveId);
const { slides, handleSlideChange, updateSlides } = useSlider(initialSlides);

// Watch for changes in archive ID and update slides accordingly
watch(() => archiveStore.currentArchiveId, (newArchiveId) => {
  const updatedSlides = createSlides(newArchiveId);
  updateSlides(updatedSlides);
}, { immediate: true });

// Handle close event
const handleClose = () => {
  archiveStore.resetArchive();
};
</script>
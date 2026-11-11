<template>
  <Slider
    :slides="slides"
    navigation-variant="box">
    <template #header>
      <SliderHeader
        title="Voreinstellungen"
        :uuid="uuid" />
    </template>
  </Slider>
</template>

<script setup>
import { markRaw, watch, computed } from 'vue';
import { usePageTitle } from '@/composables/usePageTitle';
import { useRoute } from 'vue-router';
import { getArchive } from '@/services/api/archive';
import { useUserStore } from '@/stores/user';

import Slider from '@/components/slider/Slider.vue';
import SliderHeader from '@/components/slider/Header.vue';

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

// The other slides need a saved archive
const isNew = () => !uuid.value;

const slides = [
  {
    id: 'basic-information',
    name: 'Basisinformationen',
    class: 'w-3/12 min-w-3/12',
    permission: allows('update'),
    component: markRaw(BasicInformationComponent),
  },
  {
    id: 'structure',
    name: 'Ordnung',
    class: 'w-6/12 min-w-6/12',
    permission: allows('manageStructure'),
    component: markRaw(CategoryComponent),
    disabled: isNew,
  },
  {
    id: 'card',
    name: 'Kartenvorlage',
    class: 'w-3/12 min-w-3/12',
    permission: allows('manageTemplate'),
    component: markRaw(CardComponent),
    disabled: isNew,
  },
  {
    id: 'tags',
    name: 'Tags',
    class: 'w-3/12 min-w-3/12',
    permission: allows('manageTags'),
    component: markRaw(TagsComponent),
    disabled: isNew,
  },
  {
    id: 'user',
    name: 'Benutzer',
    class: 'w-3/12 min-w-3/12',
    permission: allows('manageUsers'),
    component: markRaw(UserComponent),
    disabled: isNew,
  },
  {
    id: 'delete',
    name: 'Löschen',
    class: 'w-3/12 min-w-3/12',
    permission: allows('delete'),
    component: markRaw(DeleteComponent),
    disabled: isNew,
  },
];

const fetchArchive = async () => {
  try {
    const archive = await getArchive(uuid.value);
    setTitle(archive.name);
  } catch {
    // Only the page title; the slides report their own load errors
  }
};

watch(
  uuid,
  () => {
    if (uuid.value) {
      fetchArchive();
    } else {
      setTitle('Einstellungen');
    }
  },
  { immediate: true },
);
</script>

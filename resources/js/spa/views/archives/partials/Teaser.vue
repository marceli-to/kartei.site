<template>
  <div class="flex flex-col gap-y-32 relative h-full">
    <router-link 
      :to="{ name: 'archiveSettings', params: { uuid: archive.uuid } }"
      class="block absolute top-8 right-16 z-10 size-24"
      title="Kartei bearbeiten">
      <IconEdit class="text-graphite w-full h-auto" />
    </router-link>
    <ImageCard>
      <Image :src="archive.image.url" alt="Titelbild" spacing="m-40" />
    </ImageCard>
    <div class="border-y border-y-graphite min-h-default flex items-center">
      <h2 class="font-otto-bold">
        {{ archive.name }}
      </h2>
    </div>
    <Collapsible :items="archive.structure" v-if="archive.structure" />
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import Collapsible from '@/components/Collapsible.vue';
import IconEdit from '@/components/icons/Edit.vue';

const props = defineProps({
  archive: {
    type: Object,
    required: true
  }
});
</script>
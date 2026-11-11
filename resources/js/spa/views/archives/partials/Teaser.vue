<template>
  <div class="flex flex-col gap-y-32">
    <router-link
      v-if="archive.image"
      :to="archiveLink"
      :aria-label="archive.name">
      <ImageCard>
        <Image
          :src="archive.image.url"
          alt="Titelbild"
          spacing="m-40" />
      </ImageCard>
    </router-link>
    <div class="flex min-h-default items-center border-y border-y-graphite">
      <h2 class="font-otto-bold">
        <router-link :to="archiveLink">
          {{ archive.name }}
        </router-link>
      </h2>
    </div>
    <div>
      <Collapsible
        v-if="archive.categories"
        :items="archive.categories" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import Collapsible from '@/components/Collapsible.vue';

const props = defineProps({
  archive: {
    type: Object,
    required: true,
  },
});

const archiveLink = computed(() => ({ name: 'archiveRecords', params: { uuid: props.archive.uuid } }));
</script>

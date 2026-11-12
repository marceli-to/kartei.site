<template>
  <!--
    view: "tiles" (image, title, categories), "table" (image left, title and categories right, on the
    page's 10-column grid: 2 + 3 columns from lg up) or "images" (only the image). Without an image: an empty frame in all three views.
    Image and title link to the archive. Hover on the image: black frame, image slightly larger.
  -->
  <div :class="rootClasses">
    <router-link
      :to="archiveLink"
      :aria-label="archive.name"
      class="group block"
      :class="{ 'lg:col-span-2': view === 'table' }">
      <ImageCard classes="transition-colors group-hover:border-black dark:group-hover:border-white">
        <Image
          v-if="archive.image"
          :src="archive.image.url"
          alt="Titelbild"
          classes="absolute inset-0 flex items-center justify-center transition-transform duration-300 will-change-transform group-hover:scale-102"
          :spacing="view === 'table' ? 'm-28' : 'm-40'" />
      </ImageCard>
    </router-link>
    <div
      v-if="view !== 'images'"
      class="flex flex-col gap-y-28"
      :class="{ 'lg:col-span-3': view === 'table' }">
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
  view: {
    type: String,
    default: 'tiles',
    validator: (value) => ['tiles', 'table', 'images'].includes(value),
  },
});

const archiveLink = computed(() => ({ name: 'archiveRecords', params: { uuid: props.archive.uuid } }));

const rootClasses = computed(
  () =>
    ({
      tiles: 'flex flex-col gap-y-28',
      table: 'grid grid-cols-[1fr_2fr] gap-x-16 lg:grid-cols-subgrid',
      images: '',
    })[props.view],
);
</script>

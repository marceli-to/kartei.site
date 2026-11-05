<template>
  <div class="relative flex flex-col gap-y-32">
    <router-link
      :to="{ name: 'archiveRecords', params: { uuid: archive.uuid } }"
      class="absolute top-8 right-8 z-10 block size-24"
      title="Kartei bearbeiten">
      <Icon
        name="edit"
        class="h-auto w-full text-graphite transition-colors hover:text-black" />
    </router-link>
    <ImageCard v-if="archive.image">
      <Image
        :src="archive.image.url"
        alt="Titelbild"
        spacing="m-40" />
    </ImageCard>
    <div class="flex min-h-default items-center border-y border-y-graphite">
      <h2 class="font-otto-bold">
        {{ archive.name }}
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
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import Collapsible from '@/components/Collapsible.vue';
import Icon from '@/components/icons/Icon.vue';

defineProps({
  archive: {
    type: Object,
    required: true,
  },
});
</script>

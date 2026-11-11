<template>
  <!--
    Images: several in a slideshow, one alone; hover = black frame and a 2% larger image, a click opens the
    lightbox (this record's images only). The text block links to the record form as a whole (grey on
    hover); the heart sits on top of it (absolute), so clicking it doesn't follow the link.
  -->
  <div class="flex flex-col gap-y-28">
    <ImageSlideshow
      v-if="imageData.length > 1"
      :slides="imageData"
      @open="lightboxIndex = $event" />

    <button
      v-else-if="imageData.length === 1"
      type="button"
      class="group block w-full cursor-pointer"
      aria-label="Bild vergrössern"
      @click="lightboxIndex = 0">
      <ImageCard classes="transition-colors group-hover:border-black dark:group-hover:border-white">
        <Image
          v-bind="imageData[0]"
          classes="absolute inset-0 flex items-center justify-center transition-transform duration-300 will-change-transform group-hover:scale-102"
          spacing="m-40" />
      </ImageCard>
    </button>

    <div class="relative border-y border-y-graphite">
      <component
        :is="canEdit ? 'router-link' : 'div'"
        v-bind="canEdit ? { to: { name: 'archiveRecordUpdate', params: { uuid: record.uuid } } } : {}"
        class="block"
        :class="{ 'transition-colors hover:text-graphite': canEdit }">
        <div class="flex min-h-default items-center pr-32 font-muoto-medium">
          {{ record.display_number }}
        </div>
        <div
          v-for="(field, index) in record.fields"
          :key="index"
          class="min-h-default border-t border-t-graphite py-3">
          {{ field.content }}
        </div>
      </component>
      <Favorite
        :uuid="record.uuid"
        class="absolute top-0 right-0 flex h-32 items-center" />
    </div>

    <Lightbox
      v-model="lightboxIndex"
      :images="imageData" />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useUserStore } from '@/stores/user';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import ImageSlideshow from '@/components/media/Slideshow.vue';
import Lightbox from '@/components/media/Lightbox.vue';
import Favorite from '@/views/records/partials/Favorite.vue';

const props = defineProps({
  record: {
    type: Object,
    required: true,
  },
});

const userStore = useUserStore();
const canEdit = computed(() => userStore.canArchive(props.record.archive?.uuid, 'updateRecord'));

// Image shown in the lightbox; null = closed
const lightboxIndex = ref(null);

const media = computed(() => props.record.media || []);

const imageData = computed(() =>
  media.value.map((item) => ({
    src: item.url,
    alt: item.alt || '',
    width: item.width || undefined,
    height: item.height || undefined,
  })),
);
</script>

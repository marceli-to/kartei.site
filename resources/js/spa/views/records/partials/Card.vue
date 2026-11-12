<template>
  <!--
    Images: several in a slideshow, one alone; hover = black frame and a 2% larger image, a click opens the
    lightbox (this record's images only). The text block links to the record form as a whole (grey on
    hover); the heart sits on top of it (absolute), so clicking it doesn't follow the link.
    view (as on Meine Karteien): "tiles" (images above the text), "table" (images left, 2 columns, text right,
    3 columns, on the page's 10-column grid) or "images" (only the images). A card without an image gets an
    empty frame in all views when the archive has an image field (rows stay aligned), none "Ohne Bildfeld".
  -->
  <div :class="rootClasses">
    <div
      v-if="imageData.length || hasImages"
      :class="{ 'lg:col-span-2': view === 'table' }">
      <ImageSlideshow
        v-if="imageData.length > 1"
        :slides="imageData"
        :spacing="imageSpacing"
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
            :spacing="imageSpacing" />
        </ImageCard>
      </button>

      <component
        :is="canEdit ? 'router-link' : 'div'"
        v-else
        v-bind="canEdit ? { to: editLink, 'aria-label': record.display_number } : {}"
        class="group block">
        <ImageCard classes="transition-colors group-hover:border-black dark:group-hover:border-white" />
      </component>
    </div>

    <div
      v-if="view !== 'images'"
      class="relative border-y border-y-graphite"
      :class="{ 'self-start lg:col-span-3': view === 'table' }">
      <component
        :is="canEdit ? 'router-link' : 'div'"
        v-bind="canEdit ? { to: editLink } : {}"
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
  // The archive's card template has an image field: without images an empty frame keeps the place
  hasImages: {
    type: Boolean,
    default: true,
  },
  view: {
    type: String,
    default: 'tiles',
    validator: (value) => ['tiles', 'table', 'images'].includes(value),
  },
});

const editLink = computed(() => ({ name: 'archiveRecordUpdate', params: { uuid: props.record.uuid } }));

const rootClasses = computed(
  () =>
    ({
      tiles: 'flex flex-col gap-y-28',
      table: 'grid grid-cols-[1fr_2fr] gap-x-16 lg:grid-cols-subgrid',
      images: '',
    })[props.view],
);
// Smaller margin around the image in the narrower table column, as on Meine Karteien
const imageSpacing = computed(() => (props.view === 'table' ? 'm-28' : 'm-40'));

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

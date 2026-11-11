<template>
  <!--
    Several images in a tile: thin arrows 8px inside the frame. Hover: black frame, image 2% larger
    (as the single image in the tile); a click on an image emits "open" with its index (lightbox).
  -->
  <div class="group relative">
    <swiper
      :modules="modules"
      :navigation="{
        prevEl: prevButton,
        nextEl: nextButton,
      }"
      :loop="true"
      class="aspect-square border border-graphite transition-colors group-hover:border-black dark:group-hover:border-white">
      <swiper-slide
        v-for="(slide, index) in slides"
        :key="slide.src"
        class="cursor-pointer"
        @click="emit('open', index)">
        <Image
          :src="slide.src"
          :alt="slide.alt || `Bild ${index + 1} von ${slides.length}`"
          :width="slide.width"
          :height="slide.height"
          classes="absolute inset-0 flex items-center justify-center transition-transform duration-300 will-change-transform group-hover:scale-102"
          :spacing="'m-40'" />
      </swiper-slide>
    </swiper>

    <button
      type="button"
      aria-label="Vorheriges Bild"
      :class="`swiper-button-prev swiper-button-prev-${uniqueId} absolute top-1/2 left-0 z-10 flex h-40 w-24 -translate-y-1/2 items-center justify-start pl-8`">
      <Icon
        name="chevron-left"
        variant="thin"
        class="h-14 w-8" />
    </button>

    <button
      type="button"
      aria-label="Nächstes Bild"
      :class="`swiper-button-next swiper-button-next-${uniqueId} absolute top-1/2 right-0 z-10 flex h-40 w-24 -translate-y-1/2 items-center justify-end pr-8`">
      <Icon
        name="chevron-right"
        variant="thin"
        class="h-14 w-8" />
    </button>
  </div>
</template>
<script>
// Per-instance id for the navigation button selectors (several slideshows per page)
const instances = { count: 0 };
</script>
<script setup>
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import Image from '@/components/media/Image.vue';
import Icon from '@/components/icons/Icon.vue';

const uniqueId = ++instances.count;
const prevButton = `.swiper-button-prev-${uniqueId}`;
const nextButton = `.swiper-button-next-${uniqueId}`;

const emit = defineEmits(['open']);

defineProps({
  slides: {
    type: Array,
    required: true,
  },
});

const modules = [Navigation];
</script>

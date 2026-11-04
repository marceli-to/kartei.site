<template>
  <div class="relative">
    <swiper
      :modules="modules"
      :navigation="{
        prevEl: prevButton,
        nextEl: nextButton
      }"
      :loop="true"
      class="border border-graphite aspect-square"
    >

      <swiper-slide v-for="(slide, index) in slides" :key="slide.src">
        <Image 
          :src="slide.src"
          :alt="slide.alt || `Bild ${index + 1} von ${slides.length}`"
          :width="slide.width"
          :height="slide.height"
          :spacing="'m-40'" 
        />
      </swiper-slide>

    </swiper>

    <button type="button" aria-label="Vorheriges Bild" :class="`swiper-button-prev swiper-button-prev-${uniqueId} absolute left-0 top-1/2 -translate-y-1/2 z-10 size-40 flex justify-center items-center`">
      <Icon name="chevron-left" variant="small" />
    </button>

    <button type="button" aria-label="Nächstes Bild" :class="`swiper-button-next swiper-button-next-${uniqueId} absolute right-0 top-1/2 -translate-y-1/2 z-10 size-40 flex justify-center items-center`">
      <Icon name="chevron-right" variant="small" />
    </button>

  </div>
</template>
<script>
// Per-instance id for the navigation button selectors (several slideshows per page)
const instances = { count: 0 }
</script>
<script setup>
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import Image from '@/components/media/Image.vue';
import Icon from '@/components/icons/Icon.vue';

const uniqueId = ++instances.count
const prevButton = `.swiper-button-prev-${uniqueId}`
const nextButton = `.swiper-button-next-${uniqueId}`


defineProps({
  slides: {
    type: Array,
    required: true
  }
});

const modules = [Navigation];

</script>
<template>
  <div class="relative">
    <swiper
      :modules="modules"
      :navigation="{
        prevEl: prevButton,
        nextEl: nextButton
      }"
      :loop="true"
      @swiper="onSwiper"
      @slideChange="onSlideChange"
      class="border border-graphite aspect-square"
    >

      <swiper-slide v-for="slide in slides">
        <Image 
          :src="slide.src"
          :alt="slide.alt || 'Slide'"
          :width="slide.width || 'auto'"
          :height="slide.height || 'auto'"
          :spacing="'m-40'" 
        />
      </swiper-slide>

    </swiper>

    <button :class="`swiper-button-prev swiper-button-prev-${uniqueId} absolute left-0 top-1/2 -translate-y-1/2 z-10 size-40 flex justify-center items-center`">
      <IconChevronLeft variant="small" />
    </button>

    <button :class="`swiper-button-next swiper-button-next-${uniqueId} absolute right-0 top-1/2 -translate-y-1/2 z-10 size-40 flex justify-center items-center`">
      <IconChevronRight variant="small" />
    </button>

  </div>
</template>
<script setup>
import { ref } from 'vue'
import { v4 as uuidv4 } from 'uuid'
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import IconChevronLeft from '@/components/icons/ChevronLeft.vue';
import IconChevronRight from '@/components/icons/ChevronRight.vue';
import Image from '@/components/media/Image.vue';

const uniqueId = uuidv4()
const prevButton = `.swiper-button-prev-${uniqueId}`
const nextButton = `.swiper-button-next-${uniqueId}`


const props = defineProps({
  slides: {
    type: Array,
    required: true
  }
});

const modules = [Navigation];

const onSwiper = (swiper) => {
};

const onSlideChange = () => {
};
</script>
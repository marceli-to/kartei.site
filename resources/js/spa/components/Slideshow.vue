<template>
  <div class="relative">
    <swiper
      :modules="modules"
      :navigation="{
        prevEl: '.swiper-button-prev',
        nextEl: '.swiper-button-next'
      }"
      :loop="true"
      @swiper="onSwiper"
      @slideChange="onSlideChange"
      class="border border-graphite aspect-square"
    >

      <swiper-slide v-for="slide in slides">
        <div class="absolute inset-0 m-40 flex justify-center items-center">
          <img :src="slide" :alt="'Slide'" class="max-w-full max-h-full object-contain" />
        </div>
      </swiper-slide>

    </swiper>

    <button class="swiper-button-prev absolute left-0 top-1/2 -translate-y-1/2 z-10 size-40 flex justify-center items-center">
      <IconChevronLeft variant="small" />
    </button>

    <button class="swiper-button-next absolute right-0 top-1/2 -translate-y-1/2 z-10 size-40 flex justify-center items-center">
      <IconChevronRight variant="small" />
    </button>

  </div>
</template>
<script setup>
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import IconChevronLeft from '@/components/icons/ChevronLeft.vue';
import IconChevronRight from '@/components/icons/ChevronRight.vue';

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
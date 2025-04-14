<template>
  <div class="flex flex-col gap-y-32">
    <ImageSlideshow :slides="imageData" v-if="imageData.length > 1" />
      
    <ImageCard v-else>
      <Image v-bind="imageData[0]" />
    </ImageCard>

    <div class="border-t border-t-graphite">
      <div 
        class="min-h-default flex items-center font-muoto-medium"
        v-if="record.category">
        {{ record.display_number }}
      </div>
      <div 
        class="min-h-default flex items-center border-t border-t-graphite"
        v-for="(field, index) in record.fields"
        :key="index">
        {{ field.content }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import ImageSlideshow from '@/components/media/Slideshow.vue';

const props = defineProps({
  record: {
    type: Object,
    required: true
  },
  loopIndex: {
    type: Number,
    required: true
  }
});

const media = computed(() => props.record.media || []);

const imageData = computed(() =>
  media.value.map(item => ({
    src: item.url,
    alt: item.alt || '',
    width: item.width || 'auto',
    height: item.height || 'auto'
  }))
);

const paddedLoopIndex = computed(() => String(props.loopIndex + 1).padStart(4, '0'));

</script>
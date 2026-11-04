<template>
  <div class="flex flex-col gap-y-32">
    
    <ImageSlideshow 
      :slides="imageData" 
      v-if="imageData.length > 1" />
      
    <ImageCard v-else-if="imageData.length === 1">
      <Image v-bind="imageData[0]" />
    </ImageCard>

    <div class="border-y border-y-graphite">
      <div class="min-h-default flex items-center justify-between font-muoto-medium">
        <template v-if="record.display_number">
          <router-link v-if="canEdit" :to="{ name: 'archiveRecordUpdate', params: { uuid: record.uuid } }">
            {{ record.display_number }}
          </router-link>
          <span v-else>{{ record.display_number }}</span>
        </template>
        <Favorite :uuid="record.uuid" />
      </div>
      <div 
        class="min-h-default py-3 border-t border-t-graphite"
        v-for="(field, index) in record.fields"
        :key="index">
        {{ field.content }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useUserStore } from '@/stores/user';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import ImageSlideshow from '@/components/media/Slideshow.vue';
import Favorite from '@/views/records/components/Favorite.vue';

const props = defineProps({
  record: {
    type: Object,
    required: true
  },
});

const userStore = useUserStore();
const canEdit = computed(() => userStore.canArchive(props.record.archive?.uuid, 'updateRecord'));

const media = computed(() => props.record.media || []);

const imageData = computed(() =>
  media.value.map(item => ({
    src: item.url,
    alt: item.alt || '',
    width: item.width || undefined,
    height: item.height || undefined
  }))
);

</script>
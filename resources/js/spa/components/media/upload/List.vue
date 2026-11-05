<template>
  <div
    v-if="images.length"
    :class="multiple ? 'mt-20' : ''">
    <draggable
      v-model="list"
      :item-key="imageKey"
      :class="multiple ? 'grid grid-cols-12 gap-20' : ''">
      <template #item="{ element, index }">
        <ImageCard :class="multiple ? 'col-span-6 cursor-move xl:col-span-4' : ''">
          <Image
            :src="element.resized?.url || element.url"
            :alt="element.resized?.original_name || element.original_name || element.name" />
          <button
            v-if="editable"
            type="button"
            aria-label="Bild löschen"
            class="absolute top-0 right-0 p-8 hover:bg-snow"
            @click="deleteImage(element.url, index)">
            <Icon
              name="cross"
              variant="small" />
          </button>
        </ImageCard>
      </template>
    </draggable>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import api from '@/services/api/axios';
import { notifyError } from '@/services/api/error';
import draggable from 'vuedraggable';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import { useToastStore } from '@/stores/toast';
import Icon from '@/components/icons/Icon.vue';
const toast = useToastStore();

const props = defineProps({
  images: {
    type: Array,
    required: true,
  },
  editable: {
    type: Boolean,
    default: true,
  },
  multiple: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(['update:images']);

// No local copy: dragging hands the new order straight to the parent (v-model:images).
// vuedraggable only calls the setter when an image actually moved.
const list = computed({
  get: () => props.images,
  set: (images) => {
    emit('update:images', images);
    saveOrder(images);
  },
});

// Stable key per image: saved images have a uuid, new uploads a unique url
const imageKey = (image) => image.uuid || image.resized_name || image.url;

const saveOrder = async (images) => {
  // Only saved images (with a uuid) have a position in the database
  const positions = images
    .map((image, index) => ({ uuid: image.uuid, position: index }))
    .filter((position) => position.uuid);

  if (!positions.length) return;

  try {
    await api.put('/upload/order', { positions });
    toast.show('Reihenfolge gespeichert', 'success');
  } catch (error) {
    notifyError(error, 'Reihenfolge konnte nicht gespeichert werden');
  }
};

// Delete an image
const deleteImage = async (url, index) => {
  try {
    // Call the delete API
    await api.delete(`/upload${url}`);

    emit(
      'update:images',
      props.images.filter((_, i) => i !== index),
    );

    toast.show('Bild gelöscht', 'success');
  } catch (error) {
    notifyError(error, 'Bild konnte nicht gelöscht werden');
  }
};
</script>

<style scoped>
@reference "@/../../css/app.css";
.sortable-ghost {
  @apply bg-graphite/10;
}
</style>

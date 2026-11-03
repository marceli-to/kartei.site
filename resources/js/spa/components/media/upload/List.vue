<template>
  <div v-if="images.length" :class="multiple ? 'mt-20' : ''">
    <draggable
      v-model="localImages"
      :item-key="imageKey"
      :class="multiple ? 'grid grid-cols-12 gap-20' : ''"
      @end="onDragEnd">
      <template #item="{ element, index }">
        <ImageCard :class="multiple ? 'col-span-6 xl:col-span-4 cursor-move' : ''">
          <Image
            :src="element.resized?.url || element.url"
            :alt="element.resized?.original_name || element.original_name || element.name"
          />
           <button
            v-if="editable"
            type="button"
            @click="deleteImage((element.url), index)"
            class="absolute top-0 right-0 p-8 hover:bg-snow"
          >
            <IconCross variant="small" />
          </button>
        </ImageCard>
      </template>
    </draggable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';
import IconCross from '@/components/icons/Cross.vue';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import { useToastStore } from '@/components/toast/stores/toast';
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

const emit = defineEmits(['update:images', 'delete']);

// Create a local copy of the images for drag-and-drop
const localImages = ref([...props.images]);

// Watch for changes in the parent images and sync with localImages
watch(
  () => props.images,
  (newImages) => {
    localImages.value = [...newImages];
  }
);

// Stable key per image: saved images have a uuid, new uploads a unique url
const imageKey = (image) => image.uuid || image.resized_name || image.url;

// Emit the updated order when drag ends
const onDragEnd = async () => {
  emit('update:images', [...localImages.value]);

  // Only saved images (with a uuid) have a position in the database
  const positions = localImages.value
    .map((image, index) => ({ uuid: image.uuid, position: index }))
    .filter((position) => position.uuid);

  if (!positions.length) return;

  try {
    await axios.put('/api/upload/order', { positions });
    toast.show('Reihenfolge gespeichert', 'success');
  } catch (error) {
    console.error('Failed to update image order:', error);
    toast.show('Reihenfolge konnte nicht gespeichert werden', 'error');
  }
};

// Delete an image
const deleteImage = async (url, index) => {
  try {
    // Call the delete API
    await axios.delete(`/api/upload${url}`);

    // Emit a new list without the image (no in-place mutation)
    localImages.value = localImages.value.filter((_, i) => i !== index);
    emit('update:images', [...localImages.value]);
    emit('delete', index);

    toast.show('Bild gelöscht', 'success');
  } 
  catch (error) {
    console.error('Failed to delete image:', error);
    toast.show('Bild konnte nicht gelöscht werden', 'error');
  }
};
</script>

<style scoped>
.sortable-ghost {
  @apply bg-graphite/10
}
</style>
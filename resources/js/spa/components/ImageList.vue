<template>
  <div v-if="images.length" class="mt-24">
    <!-- Use vuedraggable for the list -->
    <draggable
      v-model="localImages"
      item-key="index"
      class="grid grid-cols-12 gap-24"
      @end="onDragEnd"
    >
      <template #item="{ element, index }">
        <figure
          class="col-span-6 border border-graphite p-20 aspect-square relative bg-white cursor-move"
        >
          <div class="absolute inset-0 m-20 flex justify-center items-center">
            <img
              :src="element.resized.url"
              :alt="element.resized.original_name"
              class="max-w-full max-h-full object-contain"
            />
          </div>
          <button
            v-if="editable"
            @click="deleteImage(element.resized.name, index)"
            class="absolute top-0 right-0 p-8 hover:bg-snow"
          >
            <IconCross variant="small" />
          </button>
         
        </figure>
      </template>
    </draggable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';
import IconCross from '@/components/icons/Cross.vue';

const props = defineProps({
  images: {
    type: Array,
    required: true,
  },
  editable: {
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

// Emit the updated order when drag ends
const onDragEnd = () => {
  emit('update:images', localImages.value);
};

// Delete an image
const deleteImage = async (imageName, index) => {
  try {
    // Call the delete API
    await axios.delete(`/api/upload/temp/${imageName}`);

    // Remove the image from the local list
    localImages.value.splice(index, 1);

    // Emit the updated list to the parent
    emit('update:images', localImages.value);

    // Emit the delete event
    emit('delete', index);
  } catch (error) {
    console.error('Failed to delete image:', error);
  }
};
</script>

<style scoped>
.sortable-ghost {
  @apply bg-graphite/10
}
</style>
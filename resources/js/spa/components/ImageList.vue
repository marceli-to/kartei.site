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
          class="col-span-6 border border-graphite p-20 aspect-square relative"
        >
          <div class="absolute inset-0 m-20 flex justify-center items-center">
            <img
              :src="element.resized.url"
              :alt="element.resized.original_name"
              class="max-w-full max-h-full object-contain"
            />
          </div>
          <!-- Delete button (if editable)
          <button
            v-if="editable"
            @click="deleteImage(index)"
            class="absolute top-8 right-8 text-red-500 hover:text-red-700"
          >
            Delete
          </button>
          -->
        </figure>
      </template>
    </draggable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import draggable from 'vuedraggable';

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
const deleteImage = (index) => {
  localImages.value.splice(index, 1);
  emit('delete', index);
  emit('update:images', localImages.value);
};
</script>
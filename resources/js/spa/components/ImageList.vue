<template>
  <div 
    v-if="images.length" 
    class="mt-24 grid grid-cols-12 gap-24"
  >
    <figure 
      v-for="(image, index) in images" 
      :key="index"
      class="col-span-6 border border-graphite p-20 aspect-square relative"
    >
      <div class="absolute inset-0 m-20 flex justify-center items-center">
        <img 
          :src="image.resized.url" 
          :alt="image.resized.original_name" 
          class="max-w-full max-h-full object-contain"
        />
      </div>
    </figure>
  </div>
</template>

<script setup>
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
const emit = defineEmits(['delete']);
const deleteImage = (index) => {
  emit('delete', index);
};
</script>
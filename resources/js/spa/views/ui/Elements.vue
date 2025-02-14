<template>
  <div class="mt-30 max-w-lg">
    <h2 class="mb-12 font-muoto-medium">Image Upload</h2>
    <ImageUpload
      :maxSize="250 * 1024 * 1024"
      :allowedTypes="['image/*', 'video/mp4']"
      uploadUrl="/api/upload"
      :multiple="false"
    />
  </div>
  <div class="mt-30 max-w-lg">
    <h2 class="mb-12 font-muoto-medium">Image Slideshow</h2>
    <Slideshow :slides="[
      'https://placehold.co/600x400',
      'https://placehold.co/400x400',
      'https://placehold.co/400x200',
      'https://placehold.co/600x900',
      ]" 
    />
  </div>
  <div class="mt-30 max-w-lg">
    <h2 class="mb-12 font-muoto-medium">Toasts</h2>
    <div class="flex gap-x-8">
      <button 
        @click="toast.show('This is a success message!', 'success')"
        class="bg-lime text-white px-8 py-2 text-sm">
        Success
      </button>
      <button 
        @click="toast.show('This is an error message!', 'error')"
        class="bg-flame text-white px-8 py-2 text-sm">
        Error
      </button>
      <button 
        @click="toast.show('This is a warning message!', 'info')"
        class="bg-ice text-white px-8 py-2 text-sm">
        Info
      </button>
    </div>
  </div>
</template>
<script setup>
import { onMounted } from 'vue';
import ImageUpload from '@/components/ImageUpload.vue';
import Slideshow from '@/components/Slideshow.vue';
import { useToastStore } from '@/store/toast';
const toast = useToastStore();

onMounted(() => {
})
</script>
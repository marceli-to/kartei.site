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
</template>
<script setup>
import ImageUpload from '@/components/ImageUpload.vue';
import Slideshow from '@/components/Slideshow.vue';
</script>
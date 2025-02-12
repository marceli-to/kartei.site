<template>
  <div class="mt-30 max-w-2xl">
    <h2 class="mb-12 font-muoto-medium">Image upload</h2>
    <ImageUpload
      :maxSize="250 * 1024 * 1024"
      :allowedTypes="['image/*', 'video/mp4']"
      uploadUrl="/api/upload"
      :multiple="false"
    />
  </div>
</template>
<script setup>
import ImageUpload from '@/components/ImageUpload.vue';
</script>
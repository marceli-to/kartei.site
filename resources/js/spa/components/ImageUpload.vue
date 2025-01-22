<template>
  <div class="image-upload">
    <div 
      class="upload-area" 
      @click="() => $refs.fileInput.click()"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      :class="{ 'drag-over': isDragging }"
    >
      Browse images...
      <input 
        type="file" 
        ref="fileInput" 
        @change="onFileChange" 
        accept="image/*" 
        style="display: none;"
      />
    </div>
    <div v-if="imageUrl" class="image-preview">
      <img :src="imageUrl" alt="Image Preview" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const imageUrl = ref(null);
const isDragging = ref(false);

const onFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    imageUrl.value = URL.createObjectURL(file);
  }
};

const handleDrop = (event) => {
  const file = event.dataTransfer.files[0];
  if (file) {
    imageUrl.value = URL.createObjectURL(file);
  }
  isDragging.value = false;
};
</script>

<style scoped>
.image-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.upload-area {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 300px;
  height: 150px;
  border: 2px dashed #ccc;
  border-radius: 5px;
  cursor: pointer;
  color: #888;
  transition: background-color 0.3s;
}

.upload-area:hover {
  background-color: #f9f9f9;
}

.upload-area.drag-over {
  background-color: #e0ffe0;
}

.image-preview {
  margin-top: 10px;
}

.image-preview img {
  max-width: 50%;
  height: auto;
}
</style>

<template>
  <div>
    <div
      ref="dropZone"
      class="border border-graphite aspect-square flex items-center justify-center w-full h-full relative cursor-pointer outline-none transition duration-200 ease-in-out group"
      :class="{
        'border-lime !text-lime': isDragging,
        'border-flame': hasError,
        'bg-snow': isUploading,
        '': isUploaded
      }"
      role="button"
      tabindex="0"
      aria-label="Upload files"
      @click="triggerFileInput"
      @keydown.space.prevent="triggerFileInput"
      @keydown.enter.prevent="triggerFileInput"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
    >
      <input
        ref="fileInput"
        type="file"
        class="hidden"
        :accept="allowedTypes.join(',')"
        :multiple="multiple"
        @change="handleFileSelect"
      />
  
      <div v-if="isUploading" class="absolute top-0 left-0 w-full">
        <div class="w-full bg-transparent h-4 overflow-hidden">
          <div 
            class="bg-black h-4 transition-all duration-200 will-change-auto" 
            :style="{ width: `${uploadProgress}%` }"
          ></div>
        </div>
        <div class="text-xs mt-4 ml-4 leading-none">{{ uploadProgress }}%</div>
      </div>

      <div v-else class="flex flex-col items-center">
        <IconImage :class="{ 'text-lime': isDragging, 'text-flame': hasError }" />
        <div class="text-sm mt-8 opacity-0 group-hover:opacity-100 transition-all duration-200">
          Click or drag & drop to upload an image
        </div>
      </div>
    </div>

    <ImageList 
      :images="uploadedFiles" 
      :editable="true" 
      @delete="handleDeleteImage"
    />

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFileUpload } from '@/composables/useFileUpload';
import ImageList from '@/components/media/ImageList.vue';
import IconImage from '@/components/icons/Image.vue';

const props = defineProps({
  maxSize: {
    type: Number,
    default: 5 * 1024 * 1024 // 5MB
  },
  allowedTypes: {
    type: Array,
    default: () => ['image/*', 'application/pdf']
  },
  uploadUrl: {
    type: String,
    default: '/api/upload'
  },
  multiple: {
    type: Boolean,
    default: true
  },
  existingImages: {
    type: Array,
    default: () => [],
  },
})

const {
  fileInput,
  isDragging,
  isUploading,
  uploadProgress,
  hasError,
  isUploaded,
  uploadedFiles,
  handleDrop,
  handleFileSelect,
  triggerFileInput,
  retryFailed,
  reset
} = useFileUpload({
  maxSize: props.maxSize,
  allowedTypes: props.allowedTypes,
  uploadUrl: props.uploadUrl,
  multiple: props.multiple
});

// Combine existing images with uploaded files
uploadedFiles.value = [...props.existingImages, ...uploadedFiles.value];

const handleDeleteImage = (index) => {
  uploadedFiles.value.splice(index, 1);
};

// Computed display of maximum size
const maxSizeMB = computed(() => props.maxSize / (1024 * 1024))

// Computed display of allowed types
const allowedTypesDisplay = computed(() => {
  return props.allowedTypes
    .map(type => type.replace('*', 'all'))
    .join(', ')
})

// Formated file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
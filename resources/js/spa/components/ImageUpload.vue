<template>
  <div class="max-w-lg">
    <div
      ref="dropZone"
      class="border border-graphite aspect-square flex items-center justify-center w-full h-full relative cursor-pointer outline-none transition duration-200 ease-in-out"
      :class="{
        'bg-snow': isDragging,
        'bg-flame': hasError,
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

      <div v-else-if="hasError" class="upload-status error">
        <p class="error-text">Upload failed</p>
        <button 
          class="retry-button"
          @click.stop="retryFailed"
        >
          Retry
        </button>
      </div>

      <div v-else>
        <IconImage />
      </div>
    </div>
    <div v-if="uploadedFiles.length">
      <ul>
        <li 
          v-for="file in uploadedFiles" 
          :key="file.name"
          class="file-item"
        >
          {{  file.path  }}
          <span class="file-name">{{ file.name }}</span>
          <span class="">{{ file.path }}</span>
          <span class="file-size">{{ formatFileSize(file.size) }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFileUpload } from '@/composables/useFileUpload';
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
  }
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
})

const maxSizeMB = computed(() => props.maxSize / (1024 * 1024))

const allowedTypesDisplay = computed(() => {
  return props.allowedTypes
    .map(type => type.replace('*', 'all'))
    .join(', ')
})

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

<style scoped>
/* .file-upload {
  width: 100%;
  max-width: 400px;
} */

.upload-area {
  width: 100%;
  min-height: 200px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  outline: none;
}

.upload-area:hover,
.upload-area:focus {
  border-color: #2196f3;
  background-color: rgba(33, 150, 243, 0.05);
}
/* 
.upload-area.drag-over {
  border-color: #2196f3;
  background-color: rgba(33, 150, 243, 0.1);
} */

.upload-area.has-error {
  border-color: #f44336;
  background-color: rgba(244, 67, 54, 0.05);
}

.upload-area.is-uploaded {
  border-color: #4caf50;
  background-color: rgba(76, 175, 80, 0.05);
}

.hidden {
  display: none;
}

.upload-icon,
.status-icon {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
}

.primary-text {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

.secondary-text {
  font-size: 0.875rem;
  color: #666;
}



.retry-button,
.reset-button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  background-color: #2196f3;
  color: white;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.retry-button:hover,
.reset-button:hover {
  background-color: #1976d2;
}

.uploaded-files {
  margin-top: 2rem;
}

.file-item {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  border-bottom: 1px solid #eee;
}

.file-icon {
  width: 24px;
  height: 24px;
  margin-right: 0.5rem;
}

.file-name {
  flex: 1;
  margin-right: 1rem;
}

.file-size {
  color: #666;
  font-size: 0.875rem;
}

.error-text {
  color: #f44336;
}

.success-text {
  color: #4caf50;
}
</style>
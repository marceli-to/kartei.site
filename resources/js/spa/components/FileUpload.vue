// components/FileUpload.vue
<template>
  <div class="file-upload">
    <div
      ref="dropZone"
      class="upload-area"
      :class="{
        'drag-over': isDragging,
        'uploading': isUploading,
        'has-error': hasError,
        'is-uploaded': isUploaded
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
      
      <div v-if="isUploading" class="upload-status">
        <div class="progress-bar">
          <div 
            class="progress-bar-fill" 
            :style="{ width: `${uploadProgress}%` }"
          ></div>
        </div>
        <p class="upload-text">Uploading... {{ uploadProgress }}%</p>
      </div>

      <div v-else-if="hasError" class="upload-status error">
        <icon-error class="status-icon" />
        <p class="error-text">Upload failed</p>
        <button 
          class="retry-button"
          @click.stop="retryFailed"
        >
          Retry
        </button>
      </div>

      <div v-else-if="isUploaded" class="upload-status success">
        <icon-check class="status-icon" />
        <p class="success-text">
          {{ uploadedFiles.length }} file(s) uploaded successfully
        </p>
        <button 
          class="reset-button"
          @click.stop="reset"
        >
          Upload More
        </button>
      </div>

      <div v-else class="upload-prompt">
        <icon-upload class="upload-icon" />
        <p class="primary-text">
          Drop files here or click to upload
        </p>
        <p class="secondary-text">
          Maximum file size: {{ maxSizeMB }}MB
        </p>
        <p class="secondary-text">
          Allowed types: {{ allowedTypesDisplay }}
        </p>
      </div>
    </div>

    <div v-if="uploadedFiles.length" class="uploaded-files">
      <h3>Uploaded Files</h3>
      <ul>
        <li 
          v-for="file in uploadedFiles" 
          :key="file.name"
          class="file-item"
        >
          <icon-file class="file-icon" />
          <span class="file-name">{{ file.name }}</span>
          <span class="file-size">{{ formatFileSize(file.size) }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFileUpload } from '../composables/useFileUpload'
import IconUpload from './icons/temp/Upload.vue'
import IconError from './icons/temp/Error.vue'
import IconCheck from './icons/temp/Check.vue'
import IconFile from './icons/temp/File.vue'

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
.file-upload {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
}

.upload-area {
  width: 100%;
  min-height: 200px;
  border: 2px dashed #ccc;
  border-radius: 8px;
  padding: 2rem;
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

.upload-area.drag-over {
  border-color: #2196f3;
  background-color: rgba(33, 150, 243, 0.1);
}

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

.progress-bar {
  width: 100%;
  height: 4px;
  background-color: #e0e0e0;
  border-radius: 2px;
  margin: 1rem 0;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background-color: #2196f3;
  transition: width 0.3s ease;
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
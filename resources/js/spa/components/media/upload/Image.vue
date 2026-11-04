<template>
  <div>
    <div
      ref="dropZone"
      class="border border-graphite aspect-square flex items-center justify-center w-full h-full relative cursor-pointer outline-none transition duration-200 ease-in-out group"
      :class="[classes, {
        '!border-[var(--theme-color)] text-[var(--theme-color)]': isDragging,
        '!border-flame': hasError,
        'bg-snow': isUploading
      }]"
      role="button"
      tabindex="0"
      aria-label="Bilder hochladen"
      @click="triggerFileInput"
      @keydown.space.prevent="triggerFileInput"
      @keydown.enter.prevent="triggerFileInput"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="onDragLeave"
      @drop.prevent="handleDrop"
      v-if="multiple || list.length === 0">
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
        <Icon name="image" :class="{ 'text-flame': hasError || hasValidationError }" />
        <template v-if="hasValidationError">
          <div class="text-sm mt-8 text-flame">
            {{ displayedError }}
          </div>
        </template>
        <template v-else>
          <div class="text-sm mt-8">
            Klicken oder Bild hierher ziehen
          </div>
        </template>
      </div>
    </div>

    <ImageList 
      :images="list"
      @update:images="images = $event" 
      :editable="true" 
      :multiple="multiple" />

  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useFileUpload, normalizeImageEntry } from '@/composables/useFileUpload.js';
import ImageList from '@/components/media/upload/List.vue';
import { MAX_UPLOAD_BYTES } from '@/config/upload';
import Icon from '@/components/icons/Icon.vue';

const props = defineProps({
  maxSize: {
    type: Number,
    default: MAX_UPLOAD_BYTES
  },
  allowedTypes: {
    type: Array,
    default: () => ['image/*']
  },
  uploadUrl: {
    type: String,
    default: '/upload'
  },
  multiple: {
    type: Boolean,
    default: true
  },
  error: {
    type: String,
    default: ''
  },
  classes: {
    type: String,
    default: ''
  }
});

// The images (saved and freshly uploaded); the parent's v-model is the only copy
const images = defineModel({ type: Array, default: () => [] });
// Forms may start with null (no image yet)
const list = computed(() => images.value ?? []);

const {
  fileInput,
  isDragging,
  isUploading,
  uploadProgress,
  hasError,
  validationErrors,
  handleDrop,
  handleFileSelect,
  triggerFileInput
} = useFileUpload({
  maxSize: props.maxSize,
  allowedTypes: props.allowedTypes,
  uploadUrl: props.uploadUrl,
  multiple: props.multiple,
  // New uploads go first; a single-image field is replaced
  onUploaded: (uploaded) => {
    images.value = props.multiple ? [...uploaded, ...list.value] : uploaded;
  }
});

const displayedError = computed(() => {
  if (props.error) return props.error;
  return validationErrors.value[0] || '';
});

const hasValidationError = computed(() => displayedError.value !== '');

// Saved images arrive in the API's shape (or [null] without image); bring them
// into the upload shape once, so new and saved images look the same
onMounted(() => {
  images.value = list.value.filter(Boolean).map(normalizeImageEntry);
});

// dragleave also fires when the pointer moves onto a child (icon, text);
// only stop the highlight when it leaves the drop zone itself
const onDragLeave = (event) => {
  if (!event.currentTarget.contains(event.relatedTarget)) {
    isDragging.value = false;
  }
};
</script>
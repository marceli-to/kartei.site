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
      v-if="multiple || uploadedFiles.length === 0">
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
        <IconImage :class="{ 'text-flame': hasError || hasValidationError }" />
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
      :images="uploadedFiles" 
      :editable="true" 
      :multiple="multiple"
      @update:images="handleImagesChange">
    </ImageList>

  </div>
</template>

<script setup>
import { computed, watch, onMounted } from 'vue'
import { useFileUpload, normalizeImageEntry } from '@/components/media/upload/composable/useFileUpload.js';
import ImageList from '@/components/media/upload/List.vue';
import { MAX_UPLOAD_BYTES } from '@/config/upload';
import IconImage from '@/components/icons/Image.vue';

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
  existingImages: {
    type: Array,
    default: () => [],
  },
  modelValue: {
    type: Array,
    default: () => []
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

const emit = defineEmits(['update:modelValue']);

const {
  fileInput,
  isDragging,
  isUploading,
  uploadProgress,
  hasError,
  validationErrors,
  uploadedFiles,
  handleDrop,
  handleFileSelect,
  triggerFileInput
} = useFileUpload({
  maxSize: props.maxSize,
  allowedTypes: props.allowedTypes,
  uploadUrl: props.uploadUrl,
  multiple: props.multiple
});

const displayedError = computed(() => {
  if (props.error) return props.error;
  return validationErrors.value[0] || '';
});

const hasValidationError = computed(() => displayedError.value !== '');

watch(uploadedFiles, (newVal) => {
  emit('update:modelValue', newVal)
});

onMounted(() => {
  const validImages = props.existingImages?.filter(Boolean) ?? []
  if (validImages.length) {
    const normalized = validImages.map(normalizeImageEntry)
    uploadedFiles.value = [...normalized]
  }
})

// dragleave also fires when the pointer moves onto a child (icon, text);
// only stop the highlight when it leaves the drop zone itself
const onDragLeave = (event) => {
  if (!event.currentTarget.contains(event.relatedTarget)) {
    isDragging.value = false;
  }
};

// Reordered or deleted in the list: replace the array so the watcher emits
const handleImagesChange = (images) => {
  uploadedFiles.value = [...images];
};
</script>
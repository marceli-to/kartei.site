import { ref } from 'vue'
import api from '@/services/api/axios'
import { notifyError } from '@/services/api/error'
import { useToastStore } from '@/components/toast/stores/toast'
import { MAX_UPLOAD_BYTES } from '@/config/upload'

export function useFileUpload(options = {}) {
  const {
    maxSize = MAX_UPLOAD_BYTES,
    allowedTypes = ['image/*', 'application/pdf'],
    uploadUrl = '/upload',
    multiple = true
  } = options;

  const toastStore = useToastStore();

  const fileInput = ref(null)
  const isDragging = ref(false)
  const isUploading = ref(false)
  const uploadProgress = ref(0)
  const hasError = ref(false)
  const validationErrors = ref([])
  const uploadedFiles = ref([])

  const toggleDrag = (value) => {
    isDragging.value = value
  }

  const validateFile = (file) => {
    const errors = []

    if (file.size > maxSize) {
      errors.push(`Die Datei "${file.name}" ist grösser als ${maxSize / (1024 * 1024)} MB`)
    }

    const isTypeAllowed = allowedTypes.some(type => {
      if (type.includes('*')) {
        const baseType = type.split('/')[0]
        return file.type.startsWith(baseType)
      }
      return file.type === type
    })

    if (!isTypeAllowed) {
      errors.push(`Der Dateityp von "${file.name}" ist nicht erlaubt`)
    }

    return {
      isValid: errors.length === 0,
      errors
    }
  }

  const processFiles = (fileList) => {
    let files = Array.from(fileList)
    if (!multiple) {
      files = files.slice(0, 1)
    }
    const validFiles = []
    const errors = []

    files.forEach(file => {
      const { isValid, errors: fileErrors } = validateFile(file)
      if (isValid) {
        validFiles.push(file)
      } else {
        errors.push(...fileErrors)
      }
    })

    return { validFiles, errors }
  }

  const uploadFiles = async (files) => {
    if (!files.length) return

    isUploading.value = true
    uploadProgress.value = 0
    hasError.value = false

    const formData = new FormData()
    files.forEach(file => formData.append('files[]', file))

    try {
      const response = await api.post(uploadUrl, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        // Own progress bar instead of the global overlay; no 10 s limit for big files
        meta: { skipLoading: true },
        timeout: 0,
        onUploadProgress: (event) => {
          // total is missing when the browser can't compute it
          if (event.total) {
            uploadProgress.value = Math.round((event.loaded * 100) / event.total)
          }
        }
      });

      const normalized = response.data.files.map(normalizeImageEntry)
      uploadedFiles.value = multiple ? [...normalized, ...uploadedFiles.value] : [...normalized]

      return response.data;
    } 
    catch (error) {
      hasError.value = true
      // 422: the server's reason (type, size, ...), otherwise the status text
      const serverMessage = error.response?.status === 422 ? error.response.data?.message : null
      notifyError(error, serverMessage || 'Das Hochladen ist fehlgeschlagen.')
    } 
    finally {
      isUploading.value = false
    }
  }

  const handleDrop = async (event) => {
    toggleDrag(false)
    const { validFiles, errors } = processFiles(event.dataTransfer.files)

    validationErrors.value = errors

    if (errors.length) {
      errors.forEach(error => {
        toastStore.show(error, 'error', 5000)
      })
    }

    if (validFiles.length) {
      await uploadFiles(validFiles)
    }
  }

  const handleFileSelect = async (event) => {
    const { validFiles, errors } = processFiles(event.target.files)

    validationErrors.value = errors

    if (errors.length) {
      errors.forEach(error => {
        toastStore.show(error, 'error', 5000)
      })
    }

    if (validFiles.length) {
      await uploadFiles(validFiles)
    }

    // Reset input value to allow uploading the same file again
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  }

  const triggerFileInput = () => {
    fileInput.value?.click()
  }

  return {
    fileInput,
    isDragging,
    isUploading,
    uploadProgress,
    hasError,
    validationErrors,
    uploadedFiles,
    handleDrop,
    handleFileSelect,
    triggerFileInput,
    normalizeImageEntry
  }
}

// Field names follow the media table: name = the file name the user uploaded,
// original_name / resized_name = the stored files (full size / resized).
// Upload responses use name for the stored file and original_name for the
// user's file name, hence the swap for fresh uploads.
export function normalizeImageEntry(entry) {
  if ('resized' in entry) {
    return {
      uuid: entry.original.uuid,
      url: entry.resized.url,
      name: entry.original.original_name,
      width: entry.resized.width,
      height: entry.resized.height,
      aspect_ratio: entry.resized.aspect_ratio,
      original_name: entry.original.name,
      resized_name: entry.resized.name,
      mime_type: entry.original.mime_type,
      size: entry.original.size
    }
  }
  return {
    uuid: entry.uuid,
    url: entry.url,
    name: entry.name,
    width: entry.width,
    height: entry.height,
    aspect_ratio: entry.aspect_ratio,
    original_name: entry.name,
    mime_type: entry.mime_type,
    size: entry.size
  }
}
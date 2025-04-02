import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useArchiveStore = defineStore('archive', () => {
  const currentArchiveId = ref(null);
  const archiveData = ref(null);
  const isArchiveCreated = ref(false);

  const setArchiveId = (id) => {
    currentArchiveId.value = id;
    isArchiveCreated.value = true;
  }

  const setArchiveData = (data) => {
    archiveData.value = data;
  }

  const resetArchive = () => {
    currentArchiveId.value = null;
    archiveData.value = null;
    isArchiveCreated.value = false;
  }

  return {
    currentArchiveId,
    archiveData,
    isArchiveCreated,
    setArchiveId,
    setArchiveData,
    resetArchive
  };
});
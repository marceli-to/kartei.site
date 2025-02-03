<template>
  <div class="mt-20" v-if="permissionStore.can('view archives')">
    <div 
      v-for="archive in archives" :key="archive.id" 
      class="border-b last-of-type:border-b-0 py-10">
      {{ archive.title }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getArchives } from '@/services/api';
import { usePermissionStore } from '@/store/permissions';
const permissionStore = usePermissionStore();
const archives = ref([]);
const isLoading = ref(true);

onMounted(async () => {
  try {
    archives.value = await getArchives();
  } 
  catch (err) {
    // error handling
  } 
  finally {
    isLoading.value = false;
  }
});
</script>
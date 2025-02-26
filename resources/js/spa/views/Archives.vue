<template>
  <div class="mt-20" v-if="userStore.can('view.archives')">
    <div 
      v-for="archive in archives" :key="archive.id" 
      class="border-b last-of-type:border-b-0 py-10">
      {{ archive.title }}
      <template v-if="userStore.can('view.archive.' + archive.id)">
        [can view]
      </template>
      <template v-if="userStore.can('edit.archive.' + archive.id)">
        [can edit]
      </template>
      <template v-if="userStore.can('delete.archive.' + archive.id)">
        [can delete]
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getArchives } from '@/services/api/archive';
import { useUserStore } from '@/stores/user';
const userStore = useUserStore();
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
<template>
  <Slide :pull="false">
    <div 
        class="flex flex-col gap-y-32" 
        v-if="!isLoading">
        <div>
          <InputSearch
            v-model="search"
            id="search"
            placeholder="Suche"
            aria-label="Suche" />
        </div>
        <div>
          <div v-for="(users, role) in groupedUsers" :key="role" class="mb-24">
            <h3 class="text-sm mb-4 block">{{ role }}</h3>
            <div class="flex flex-col gap-y-8">
              <Action 
                v-for="user in users" 
                :key="user.uuid"
                :label="`${user.firstname} ${user.name}`"
                :icon="{ name: 'ChevronRight' }" />
            </div>
          </div>
        </div>
    </div>
  </Slide>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue';
import { getArchivesByAdmin } from '@/services/api/archive';
import { getRelatedUsers } from '@/services/api/user';
import Slide from '@/components/slider/Slide.vue';
import InputSearch from '@/components/forms/Search.vue';
import Action from '@/components/buttons/Action.vue';

const archives = ref([]);
const relatedUsers = ref([]);
const search = ref('');
const isLoading = ref(true);

onMounted(async () => {
  try {
    isLoading.value = true;
    const archivesResponse = await getArchivesByAdmin();
    archives.value = archivesResponse;
    const relatedUsersResponse = await getRelatedUsers();
    relatedUsers.value = relatedUsersResponse.data || [];
  }
  catch (error) {
    console.error(error);
  } 
  finally {
    isLoading.value = false;
  }
});

const filteredRelatedUsers = computed(() => {
  if (!relatedUsers.value || !Array.isArray(relatedUsers.value)) return [];
  
  let result = [...relatedUsers.value];
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase().trim();
    result = result.filter(user => 
      user.firstname?.toLowerCase().includes(searchTerm) ||
      user.name?.toLowerCase().includes(searchTerm) ||
      user.email?.toLowerCase().includes(searchTerm)
    );
  }
  
  return result;
});

const groupedUsers = computed(() => {
  const users = filteredRelatedUsers.value;
  if (!users.length) return {};
  
  // Group users by role
  const grouped = users.reduce((acc, user) => {
    // Get the role name, default to 'Other' if no role
    const role = user.role;
    
    // Initialize the role group if it doesn't exist
    if (!acc[role]) {
      acc[role] = [];
    }
    
    // Add the user to the appropriate role group
    acc[role].push(user);
    
    return acc;
  }, {});
  
  return grouped;
});
</script>
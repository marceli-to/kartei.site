<template>
  <div class="flex flex-col gap-y-32" v-if="!isLoading">
    <div>
      <InputSearch
        v-model="searchQuery"
        placeholder="Suche"
        aria-label="Suche" />
    </div>
    <div v-for="(users, role) in groupedUsers" :key="role" class="mb-24">
      <h3 class="text-sm mb-4 block">
        {{ role || 'Andere' }}
      </h3>
      <div class="flex flex-col gap-y-8">
        <Action 
          v-for="user in users" 
          :key="user.uuid"
          :label="`${user.firstname} ${user.name}`"
          :icon="{ name: 'ChevronRight' }"
          @click="$emit('user-selected', user)" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { getRelatedUsers } from '@/services/api/user';
import InputSearch from '@/components/forms/Search.vue';
import Action from '@/components/buttons/Action.vue';

const users = ref([]);
const searchQuery = ref('');
const isLoading = ref(true);

onMounted(async () => {
  await fetchUsers();
});

const fetchUsers = async () => {
  try {
    isLoading.value = true;
    const response = await getRelatedUsers();
    users.value = response.data || [];
    console.log(users.value);
  } catch (error) {
    console.error('Failed to fetch users:', error);
  } finally {
    isLoading.value = false;
  }
};

const filteredUsers = computed(() => {
  if (!users.value || !Array.isArray(users.value)) return [];
  
  if (!searchQuery.value) return users.value;
  
  const searchTerm = searchQuery.value.toLowerCase().trim();
  return users.value.filter(user => 
    user.firstname?.toLowerCase().includes(searchTerm) ||
    user.name?.toLowerCase().includes(searchTerm) ||
    user.email?.toLowerCase().includes(searchTerm)
  );
});

const groupedUsers = computed(() => {
  const filteredUserList = filteredUsers.value;
  if (!filteredUserList.length) return {};
  
  // Group users by role
  return filteredUserList.reduce((acc, user) => {
    // Get the role name, default to 'Andere' if no role
    const role = user.role || 'Andere';
    
    // Initialize the role group if it doesn't exist
    if (!acc[role]) {
      acc[role] = [];
    }
    
    // Add the user to the appropriate role group
    acc[role].push(user);
    
    return acc;
  }, {});
});

// Expose necessary methods to parent component
defineExpose({
  fetchUsers,
  resetSearch: () => { searchQuery.value = ''; }
});
</script>
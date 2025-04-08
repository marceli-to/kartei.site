<template>
  <div class="flex flex-col gap-y-16" v-if="!isLoading">
    <div>
      <InputSearch
        v-model="searchQuery"
        placeholder="Suche"
        aria-label="Suche" />
    </div>
    <div class="flex flex-col gap-y-20">
      <div v-for="(users, role) in groupedUsers" :key="role">
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
    <div>
      <Action 
        label="Benutzer/in" 
        :icon="{ name: 'Plus', position: 'center' }"
        @click="$emit('create-user')" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import InputSearch from '@/components/forms/Search.vue';
import Action from '@/components/buttons/Action.vue';

const searchQuery = ref('');

// Props from parent
const props = defineProps({
  users: {
    type: Array,
    default: () => []
  },
  isLoading: {
    type: Boolean,
    default: false
  }
});

// Emits
const emit = defineEmits(['user-selected', 'create-user']);

const filteredUsers = computed(() => {
  if (!props.users || !Array.isArray(props.users)) return [];
  
  if (!searchQuery.value) return props.users;
  
  const searchTerm = searchQuery.value.toLowerCase().trim();
  return props.users.filter(user => 
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
  resetSearch: () => { searchQuery.value = ''; }
});
</script>
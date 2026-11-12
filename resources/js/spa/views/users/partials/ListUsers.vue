<template>
  <div
    v-if="!isLoading"
    class="flex flex-col gap-y-16">
    <div>
      <InputSearch
        v-model="searchQuery"
        placeholder="Suchen"
        aria-label="Suchen" />
    </div>
    <div class="flex flex-col gap-y-20">
      <div
        v-for="(roleUsers, role) in groupedUsers"
        :key="role">
        <h3 class="mb-4 block text-sm">
          {{ role || 'Andere' }}
        </h3>
        <div class="flex flex-col gap-y-8">
          <Action
            v-for="user in roleUsers"
            :key="user.uuid"
            :label="`${user.firstname} ${user.name}`"
            :icon="{ name: 'Edit' }"
            @click="$emit('user-selected', user)" />
        </div>
      </div>
    </div>
    <!-- Adding comes after the list. Only adding is blocked without a subscription or seats -->
    <div class="mt-16">
      <Action
        :label="'Benutzer\u2009/\u2009in'"
        :icon="{ name: 'Plus', position: 'center' }"
        :disabled="!canCreate"
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
    default: () => [],
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  canCreate: {
    type: Boolean,
    default: true,
  },
});

// Emits
defineEmits(['user-selected', 'create-user']);

const filteredUsers = computed(() => {
  if (!props.users || !Array.isArray(props.users)) return [];

  if (!searchQuery.value) return props.users;

  const searchTerm = searchQuery.value.toLowerCase().trim();
  return props.users.filter(
    (user) =>
      user.firstname?.toLowerCase().includes(searchTerm) ||
      user.name?.toLowerCase().includes(searchTerm) ||
      user.email?.toLowerCase().includes(searchTerm),
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
  resetSearch: () => {
    searchQuery.value = '';
  },
});
</script>

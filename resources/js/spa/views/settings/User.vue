<template>
  <Slide :pull="viewState !== 'listing' && viewState !== 'notifying'">

    <template v-if="viewState === 'creating'">
      <CreateUser 
        @success="(userData) => handleUserCreated(userData)" 
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'updating'">
      <UpdateUser 
        :uuid="selectedUser.uuid"
        @success="handleUserUpdated"
        @user-deleted="handleUserDeleted"
        @user-selected-permissions="handleUserSelectedPermissions"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'createPermissions'">
      <CreateUserPermissions 
        :user="createdUser"
        @success="handleUserPermissionsCreated"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'updatePermissions'">
      <UpdateUserPermissions 
        :user="selectedUser"
        @success="handleUserPermissionsUpdated"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'listing'">
      <ListUsers 
        ref="userListRef"
        :users="users"
        :isLoading="isLoadingUsers"
        @user-selected="handleUserSelected"
        @create-user="viewState = 'creating'" />
    </template>

  </Slide>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { getRelatedUsers } from '@/services/api/archiveUser';
import Slide from '@/components/slider/Slide.vue';
import CreateUser from '@/views/settings/partials/CreateUser.vue';
import UpdateUser from '@/views/settings/partials/UpdateUser.vue';
import CreateUserPermissions from '@/views/settings/partials/CreateUserPermissions.vue';
import UpdateUserPermissions from '@/views/settings/partials/UpdateUserPermissions.vue';
import ListUsers from '@/views/settings/partials/ListUsers.vue';

const userListRef = ref(null);
const createdUser = ref(null);
const selectedUser = ref(null);
const viewState = ref('listing');
const users = ref([]);
const isLoadingUsers = ref(false);

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

// Fetch users from API
const fetchUsers = async () => {
  try {
    isLoadingUsers.value = true;
    const response = await getRelatedUsers();
    users.value = response.data || [];
  } catch (error) {
    console.error('Failed to fetch users:', error);
  } finally {
    isLoadingUsers.value = false;
  }
};

// Load users when component mounts
onMounted(() => {
  fetchUsers();
});

const handleUserSelected = (user) => {
  selectedUser.value = user;
  viewState.value = 'updating';
};

const handleUserCreated = (userData) => {
  createdUser.value = userData;
  viewState.value = 'createPermissions';
};

const handleUserUpdated = () => {
  resetView();
  fetchUsers();
};

const handleUserPermissionsCreated = () => {
  resetView();
  fetchUsers();
};

const handleUserSelectedPermissions = (user) => {
  selectedUser.value = user;
  viewState.value = 'updatePermissions';
}; 

const handleUserPermissionsUpdated = () => {
  resetView();
  fetchUsers();
};

const handleUserDeleted = () => {
  resetView();
  fetchUsers();
};

const resetView = () => {
  const previousState = viewState.value;
  viewState.value = 'listing';
  
  if (previousState !== 'updating') {
    createdUser.value = null;
  }
  
  // Reset selected user when going back to listing
  selectedUser.value = null;
  
  if (userListRef.value) {
    userListRef.value.resetSearch();
  }
};

// Watch for when this slide becomes active
watch(() => props.isActive, (isActive) => {
  if (isActive) {
    resetView();
    fetchUsers(); // Refresh users when slide becomes active
  }
});
</script>
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
        :archiveUuid="archiveUuid"
        @success="handleUserPermissionsCreated"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'updatePermissions'">
      <UpdateUserPermissions 
        :user="selectedUser"
        :archiveUuid="archiveUuid"
        @success="handleUserPermissionsUpdated"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'listing'">
      <ListUsers 
        ref="userListRef"
        :users="users"
        :isLoading="isLoadingUsers"
        @user-selected="handleUserSelected"
        @create-user="viewState = 'creating'"
        :class="{ 'opacity-50 pointer-events-none': disableListUsers }" />

      <SubscriptionInfo 
        :users="users" 
        :isActive="props.isActive"
        @update:disableListUsers="disableListUsers = $event" />

    </template>
  </Slide>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { getRelatedUsers, getArchiveUsers } from '@/services/api/archiveUser';
import Slide from '@/components/slider/Slide.vue';
import CreateUser from '@/views/users/partials/CreateUser.vue';
import UpdateUser from '@/views/users/partials/UpdateUser.vue';
import CreateUserPermissions from '@/views/users/partials/CreateUserPermissions.vue';
import UpdateUserPermissions from '@/views/users/partials/UpdateUserPermissions.vue';
import ListUsers from '@/views/users/partials/ListUsers.vue';
import SubscriptionInfo from '@/views/users/components/SubscriptionInfo.vue';

const route = useRoute();
const archiveUuid = ref(route.params.uuid || null);
const userListRef = ref(null);
const createdUser = ref(null);
const selectedUser = ref(null);
const users = ref([]);
const viewState = ref('listing');
const disableListUsers = ref(true); // default to true until info is available
const isLoadingUsers = ref(false);

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

// Watch for when this slide becomes active
watch(() => props.isActive, async (isActive) => {
  if (isActive) {
    resetView();
    await fetchUsers();
  }
});

// Load users when component mounts
onMounted( async ()  => {
 if (props.isActive) {
    await fetchUsers();
 }
});

// Fetch users
const fetchUsers = async () => {
  try {
    isLoadingUsers.value = true;
    let response;
    if (archiveUuid.value) {
      response = await getArchiveUsers(archiveUuid.value);
    }
    else {
      response = await getRelatedUsers();
    }
    users.value = response.data || [];
  } 
  catch (error) {
    console.error('Failed to fetch users:', error);
  }
  finally {
    isLoadingUsers.value = false;
  }
};

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

const handleUserDeleted = (deletedUserUuid) => {
  users.value = users.value.filter(user => user.uuid !== deletedUserUuid);
  resetView();
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
</script>
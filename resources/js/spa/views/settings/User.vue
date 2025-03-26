<template>
  <Slide :pull="viewState !== 'listing' && viewState !== 'notifying'">

    <template v-if="viewState === 'creating'">
      <CreateUser 
        @success="(userData) => handleUserCreated(userData)" 
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'updating'">
    </template>

    <template v-if="viewState === 'settingPermissions'">
      <CreateUserPermissions 
        :user="createdUser"
        @success="handleUserPermissionsUpdated"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'notifying'">
      <InviteUser 
        :user="createdUser"
        @success="handleUserNotified"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'listing'">
      <div class="flex flex-col gap-y-48">
        <!-- User list with search-->
        <ListUsers 
          ref="userListRef"
          @user-selected="handleUserSelected"
          @create-user="viewState = 'creating'" />
        <!-- // User list with search-->
      </div>
    </template>

  </Slide>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import Slide from '@/components/slider/Slide.vue';
import CreateUser from '@/views/settings/partials/CreateUser.vue';
import CreateUserPermissions from '@/views/settings/partials/CreateUserPermissions.vue';
import InviteUser from '@/views/settings/partials/InviteUser.vue';
import ListUsers from '@/views/settings/partials/ListUsers.vue';

const userListRef = ref(null);
const createdUser = ref(null);
const viewState = ref('listing');

const handleUserSelected = (user) => {
  console.log('User selected:', user);
};

const handleUserCreated = (userData) => {
  createdUser.value = userData;
  viewState.value = 'settingPermissions';
};

const handleUserPermissionsUpdated = () => {
  viewState.value = 'notifying';
};

const handleUserNotified = () => {
  resetView();
  refreshUserList();
};

const refreshUserList = () => {
  if (userListRef.value) {
    userListRef.value.fetchUsers();
  }
};

const resetView = () => {
  const previousState = viewState.value;
  viewState.value = 'listing';
  
  if (previousState !== 'updating') {
    createdUser.value = null;
  }
  
  if (userListRef.value) {
    userListRef.value.resetSearch();
  }
};
</script>
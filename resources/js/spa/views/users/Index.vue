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
        :class="{ 'opacity-50 pointer-events-none': showSubscriptionInfo }" />
        
        <SubscriptionInfo v-if="showSubscriptionInfo">
          <template v-if="hasSubscription && maxUsersReached">
            Die maximale Anzahl von Benutzern für dein Abonnement ist erreicht. Um weitere Benutzer hinzuzufügen, musst du dein Abonnement upgraden.
          </template>
          <template v-if="!hasSubscription">
            Du hast noch kein Abonnement. Um Benutzer hinzuzufügen, musst du ein Abonnement erstellen.
          </template>
        </SubscriptionInfo>
    </template>


  </Slide>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { getRelatedUsers, getArchiveUsers } from '@/services/api/archiveUser';
import { getUserSubscription } from '@/services/api/user';
import Slide from '@/components/slider/Slide.vue';
import CreateUser from '@/views/users/partials/CreateUser.vue';
import UpdateUser from '@/views/users/partials/UpdateUser.vue';
import CreateUserPermissions from '@/views/users/partials/CreateUserPermissions.vue';
import UpdateUserPermissions from '@/views/users/partials/UpdateUserPermissions.vue';
import ListUsers from '@/views/users/partials/ListUsers.vue';
import SubscriptionInfo from '@/views/users/partials/SubscriptionInfo.vue';

const route = useRoute();
const archiveUuid = ref(route.params.uuid || null);

const userListRef = ref(null);
const createdUser = ref(null);
const selectedUser = ref(null);
const users = ref([]);
const viewState = ref('listing');
const isLoadingUsers = ref(false);

const subscription = ref(null);
const hasSubscription = computed(() => subscription.value !== null);
const maxUsersReached = computed(() => {
  if (!subscription.value || !subscription.value.max_users) return false;
  return users.value.length >= subscription.value.max_users;
});

const showSubscriptionInfo = computed(() => {
  return !hasSubscription.value || (maxUsersReached.value || !hasSubscription.value);
});

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

// Load users when component mounts
onMounted( async ()  => {
 if (props.isActive) {
    await Promise.all([
      fetchUsers(), 
      fetchSubscription()
    ]);
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

// Fetch subscription
const fetchSubscription = async () => {
  try {
    const response = await getUserSubscription();
    subscription.value = response.data || null;
  } 
  catch (error) {
    console.error('Failed to fetch users subscription:', error);
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
watch(() => props.isActive, async (isActive) => {
  if (isActive) {
    resetView();
    await Promise.all([
      fetchUsers(), 
      fetchSubscription()
    ]);
  }
});
</script>
<template>
  <!-- The edit box starts on the line; the other forms start with a label above it -->
  <Slide :pull="!['listing', 'notifying', 'updating'].includes(viewState)">
    <template v-if="viewState === 'creating'">
      <UserForm
        @success="(userData) => handleUserCreated(userData)"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'updating'">
      <UserEdit
        :user="selectedUser"
        :archive-uuid="archiveUuid"
        @success="handleUserUpdated"
        @user-deleted="handleUserDeleted"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'createPermissions'">
      <UserPermissions
        :user="createdUser"
        :archive-uuid="archiveUuid"
        @success="handleUserPermissionsSaved" />
    </template>

    <template v-if="viewState === 'listing'">
      <ListUsers
        ref="userListRef"
        :users="users"
        :is-loading="isLoadingUsers"
        :can-create="!showSubscriptionInfo"
        @user-selected="handleUserSelected"
        @create-user="viewState = 'creating'" />

      <SubscriptionInfo
        v-if="showSubscriptionInfo"
        :has-subscription="hasSubscription"
        :max-users-reached="maxUsersReached" />
    </template>
  </Slide>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { getRelatedUsers, getArchiveUsers } from '@/services/api/archiveUser';
import { getUserSubscription } from '@/services/api/subscription';
import { notifyError } from '@/services/api/error';
import Slide from '@/components/slider/Slide.vue';
import UserForm from '@/views/users/partials/UserForm.vue';
import UserPermissions from '@/views/users/partials/UserPermissions.vue';
import UserEdit from '@/views/users/partials/UserEdit.vue';
import ListUsers from '@/views/users/partials/ListUsers.vue';
import SubscriptionInfo from '@/views/users/partials/SubscriptionInfo.vue';

const route = useRoute();
const archiveUuid = ref(route.params.uuid || null);
const userListRef = ref(null);
const createdUser = ref(null);
const selectedUser = ref(null);
const users = ref([]);
const viewState = ref('listing');
// true until the first load, so the subscription note doesn't flash
const isLoadingUsers = ref(true);
const subscription = ref(null);
const isLoadingSubscription = ref(true);

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false,
  },
});

// Subscription logic
const hasSubscription = computed(() => subscription.value !== null);

const maxUsersReached = computed(() => {
  if (!hasSubscription.value) return false;
  return users.value.length >= subscription.value.max_users;
});

const showSubscriptionInfo = computed(() => {
  // The seat limit needs both the subscription and the user count
  if (!props.isActive || isLoadingSubscription.value || isLoadingUsers.value) return false;
  return !hasSubscription.value || maxUsersReached.value;
});

// Watch for when this slide becomes active
watch(
  () => props.isActive,
  async (isActive) => {
    if (isActive) {
      resetView();
      await Promise.all([fetchUsers(), fetchSubscription()]);
    }
  },
);

// Load users when component mounts
onMounted(async () => {
  if (props.isActive) {
    await Promise.all([fetchUsers(), fetchSubscription()]);
  }
});

// Fetch subscription
const fetchSubscription = async () => {
  if (!props.isActive) return;
  try {
    isLoadingSubscription.value = true;
    const response = await getUserSubscription();
    subscription.value = response.data || null;
  } catch (error) {
    notifyError(error, 'Das Abonnement konnte nicht geladen werden.');
  } finally {
    isLoadingSubscription.value = false;
  }
};

// Fetch users
const fetchUsers = async () => {
  try {
    isLoadingUsers.value = true;
    let response;
    if (archiveUuid.value) {
      response = await getArchiveUsers(archiveUuid.value);
    } else {
      response = await getRelatedUsers();
    }
    users.value = response.data || [];
  } catch (error) {
    notifyError(error, 'Die Benutzer/innen konnten nicht geladen werden.');
  } finally {
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

const handleUserPermissionsSaved = () => {
  resetView();
  fetchUsers();
};

const handleUserDeleted = (deletedUserUuid) => {
  users.value = users.value.filter((user) => user.uuid !== deletedUserUuid);
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

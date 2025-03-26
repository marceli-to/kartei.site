<template>
  <Slide :pull="viewState !== 'listing' && viewState !== 'notifying'">

    <template v-if="viewState === 'creating'">
      <CreateUser 
        :archives="selectedArchives"
        @success="(userData) => handleUserCreated(userData)" 
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'updating'">
    </template>

    <template v-if="viewState === 'settingPermissions'">
      <CreateUserPermissions 
        :user="createdUser"
        :selectedArchives="selectedArchives"
        @success="handleUserPermissionsUpdated"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'notifying'">
      <InviteUser 
        :user="createdUser"
        :selectedArchives="selectedArchives"
        @success="handleUserNotified"
        @cancel="resetView()" />
    </template>

    <template v-if="viewState === 'listing'">
      <div class="flex flex-col gap-y-48">

        <!-- User list with search-->
        <ListUsers 
          ref="userListRef"
          @user-selected="handleUserSelected" />
        <!-- // User list with search-->
        
        <!-- Archive selection -->
        <div>
          <div class="flex flex-col gap-y-32">
            <div>
              <h3 class="text-sm mb-4 block">
                <template v-if="selectedArchives.length == 0">
                  Kartei/en auswählen
                </template>
                <template v-else>
                  Benutzer/in hinzufügen
                </template>
              </h3>
              <InputSelectButtons
                v-model="selectedArchives"
                :multiple="true"
                name="archives"
                wrapperClasses="flex flex-col gap-y-8"
                :options="archives" />            
            </div>
            <div>
              <Action 
                label="Benutzer/in" 
                :icon="{ name: 'Plus', position: 'center' }"
                :disabled="selectedArchives.length == 0"
                @click="viewState = 'creating'" />
            </div>
          </div>
        </div>
        <!-- // Archive selection -->
      </div>
    </template>

  </Slide>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { getArchivesByAdmin } from '@/services/api/archive';
import Slide from '@/components/slider/Slide.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import Action from '@/components/buttons/Action.vue';
import CreateUser from '@/views/settings/partials/CreateUser.vue';
import CreateUserPermissions from '@/views/settings/partials/CreateUserPermissions.vue';
import InviteUser from '@/views/settings/partials/InviteUser.vue';
import ListUsers from '@/views/settings/partials/ListUsers.vue';

const archives = ref([]);
const selectedArchives = ref([]);
const userListRef = ref(null);
const createdUser = ref(null);
const viewState = ref('listing');
const isLoading = ref(true);

onMounted(async () => {
  try {
    isLoading.value = true;
    const archivesResponse = await getArchivesByAdmin();
    archives.value = archivesResponse.map(archive => ({
      value: archive.uuid,
      label: archive.title
    }));
  }
  catch (error) {
    console.error('Failed to fetch archives:', error);
  } 
  finally {
    isLoading.value = false;
  }
});

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
  selectedArchives.value = [];
  
  if (previousState !== 'updating') {
    createdUser.value = null;
  }
  
  if (previousState === 'notifying') {
    selectedArchives.value = [];
  }
  
  if (userListRef.value) {
    userListRef.value.resetSearch();
  }
};
</script>
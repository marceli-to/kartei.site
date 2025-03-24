<template>
  <Slide :pull="isCreating || isUpdating">
    <template v-if="isCreating">
      <CreateUser 
        :archives="selectedArchives"
        @success="handleUserCreated" 
        @cancel="isCreating = false" />
    </template>
    <template v-else>
      <div class="flex flex-col gap-y-48">

        <!-- User list with search-->
        <div class="flex flex-col gap-y-32" v-if="!isLoading">
          <div>
            <InputSearch
              v-model="search"
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
                :disabled="selectedArchives.length == 0 ? true : false"
                @click="isCreating = true" />
            </div>
          </div>
        </div>
        <!-- // Archive selection -->
      </div>
    </template>
  </Slide>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue';
import { getArchivesByAdmin } from '@/services/api/archive';
import { getRelatedUsers } from '@/services/api/user';
import Slide from '@/components/slider/Slide.vue';
import InputSearch from '@/components/forms/Search.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import Action from '@/components/buttons/Action.vue';
import CreateUser from '@/views/settings/partials/CreateUser.vue';

const archives = ref([]);
const relatedUsers = ref([]);
const search = ref('');
const selectedArchives = ref([]);
const isLoading = ref(true);
const isCreating = ref(false);
const isUpdating = ref(false);

onMounted(async () => {
  try {
    isLoading.value = true;
    const archivesResponse = await getArchivesByAdmin();

    archives.value = archivesResponse.map(archive => ({
      value: archive.uuid, // Use appropriate unique identifier
      label: archive.title // Use appropriate display field
    }));


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

const handleUserCreated = () => {
  isCreating.value = false;
};

const handleUserUpdated = () => {
  isUpdating.value = false;
};

</script>
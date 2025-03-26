<template>
  <Slide :pull="viewState !== 'listing'">
    <template v-if="viewState === 'creating'">
      <CreateCompany 
        @success="handleCompanyCreated" 
        @cancel="resetView()" />
    </template>
    <template v-else-if="viewState === 'updating'">
      <UpdateCompany 
        :company="selectedCompany"
        @delete="handleCompanyDeleted"
        @success="handleCompanyUpdated" 
        @cancel="resetView()" />
    </template>
    <template v-else-if="viewState === 'listing'">
      <div 
        class="flex flex-col gap-y-32" 
        v-if="!isLoading">
        <div>
          <InputSearch
            v-model="search"
            placeholder="Suche"
            aria-label="Suche" />
        </div>
        <div>
          <Action 
            label="Kunde/Kundin" 
            :icon="{ name: 'Plus', position: 'center' }"
            @click="viewState = 'creating'" />
        </div>
        <div>
          <Sort 
            :isAscending="isAscending" 
            @toggle="sort">
            A&thinsp;–&thinsp;Z
          </Sort>
          <div class="flex flex-col gap-y-8">
            <Action 
              v-for="company in filteredCompanies" 
              :key="company.uuid"
              :label="company.name"
              :icon="{ name: 'ChevronRight' }"
              @click="showCompany(company)" />
          </div>
        </div>
      </div>
    </template>
  </Slide>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { getUserCompanies } from '@/services/api/user';
import Slide from '@/components/slider/Slide.vue';
import InputSearch from '@/components/forms/Search.vue';
import Action from '@/components/buttons/Action.vue';
import Sort from '@/components/buttons/Sort.vue';
import CreateCompany from '@/views/settings/partials/CreateCompany.vue';
import UpdateCompany from '@/views/settings/partials/UpdateCompany.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const search = ref('');
const companies = ref([]);
const selectedCompany = ref(null);
const isAscending = ref(true);
const isLoading = ref(true);
const viewState = ref('listing');

const filteredCompanies = computed(() => {
  if (!companies.value) return [];
  
  let result = [...companies.value];
  
  // Apply search filter if search term exists
  if (search.value) {
    const searchTerm = search.value.toLowerCase().trim();
    result = result.filter(company => 
      company.name.toLowerCase().includes(searchTerm) ||
      company.street.toLowerCase().includes(searchTerm) ||
      company.city.toLowerCase().includes(searchTerm)
    );
  }
  
  // Apply sorting
  return result.sort((a, b) => {
    const modifier = isAscending.value ? 1 : -1;
    return a.name.localeCompare(b.name) * modifier;
  });
});

const sort = () => {
  isAscending.value = !isAscending.value;
};

const showCompany = (company) => {
  selectedCompany.value = company;
  viewState.value = 'updating';
};

const handleCompanyCreated = (company) => {
  companies.value.push(company);
  toast.show('Firma erfolgreich erstellt.', 'success');
  resetView();
};

const handleCompanyUpdated = (company) => {
  companies.value = companies.value.map(c => c.uuid === company.uuid ? company : c);
  toast.show('Firma erfolgreich aktualisiert.', 'success');
  resetView();
};  

const handleCompanyDeleted = (uuid) => {
  companies.value = companies.value.filter(c => c.uuid !== uuid);
  toast.show('Firma erfolgreich gelöscht.', 'success');
  resetView();
};

const resetView = () => {
  viewState.value = 'listing';
  // Reset company when going back to listing only if needed
  if (viewState.value !== 'updating') {
    selectedCompany.value = null;
  }
};

onMounted(async () => {
  try {
    const response = await getUserCompanies();
    companies.value = response.data;
  } 
  catch (error) {
    toast.show('Fehler beim Laden der Firmen.', 'error');
  } 
  finally {
    isLoading.value = false;
  }
});

watch(() => props.isActive, (isActive) => {
  if (isActive) {
    resetView();
  }
});
</script>
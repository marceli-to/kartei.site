<template>
  <!-- Only the create form starts with a label above the line; the edit box starts on it -->
  <Slide :pull="viewState === 'creating'">
    <template v-if="viewState === 'creating'">
      <CompanyForm
        @success="handleCompanyCreated"
        @cancel="resetView()" />
    </template>
    <template v-else-if="viewState === 'updating'">
      <CompanyForm
        :company="selectedCompany"
        @delete="handleCompanyDeleted"
        @success="handleCompanyUpdated"
        @cancel="resetView()" />
    </template>
    <template v-else-if="viewState === 'listing'">
      <div
        v-if="!isLoading"
        class="flex flex-col gap-y-32">
        <div>
          <InputSearch
            v-model="search"
            placeholder="Suche"
            aria-label="Suche" />
        </div>
        <div>
          <Action
            :label="'Kundin\u2009/\u2009Kunde'"
            :icon="{ name: 'Plus', position: 'center' }"
            @click="viewState = 'creating'" />
        </div>
        <div>
          <Sort
            :is-ascending="isAscending"
            @toggle="sort">
            A&thinsp;–&thinsp;Z
          </Sort>
          <div class="flex flex-col gap-y-8">
            <Action
              v-for="company in filteredCompanies"
              :key="company.uuid"
              :label="company.name"
              :icon="{ name: 'Edit' }"
              @click="showCompany(company)" />
          </div>
        </div>
      </div>
    </template>
  </Slide>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useToastStore } from '@/stores/toast';
import { notifyError } from '@/services/api/error';
import { getUserCompanies } from '@/services/api/company';
import Slide from '@/components/slider/Slide.vue';
import InputSearch from '@/components/forms/Search.vue';
import Action from '@/components/buttons/Action.vue';
import Sort from '@/components/buttons/Sort.vue';
import CompanyForm from '@/views/settings/partials/CompanyForm.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false,
  },
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
    // street and city are optional
    result = result.filter((company) =>
      [company.name, company.street, company.city].some((value) => (value ?? '').toLowerCase().includes(searchTerm)),
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
  toast.show('Kunde erfolgreich erstellt.', 'success');
  resetView();
};

const handleCompanyUpdated = (company) => {
  companies.value = companies.value.map((c) => (c.uuid === company.uuid ? company : c));
  toast.show('Kunde erfolgreich aktualisiert.', 'success');
  resetView();
};

const handleCompanyDeleted = (uuid) => {
  companies.value = companies.value.filter((c) => c.uuid !== uuid);
  toast.show('Kunde erfolgreich gelöscht.', 'success');
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
  } catch (error) {
    notifyError(error, 'Die Kunden konnten nicht geladen werden.');
  } finally {
    isLoading.value = false;
  }
});

watch(
  () => props.isActive,
  (isActive) => {
    if (isActive) {
      resetView();
    }
  },
);
</script>

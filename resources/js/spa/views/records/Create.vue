<template>
  <div class="flex flex-grow w-full">
    <div class="flex mt-106 min-h-full w-full">
      <ContentNavigation>
        <RecordsNavigation
          v-model="filters"
          :uuid="uuid"
          :disabled="true" />
      </ContentNavigation>

      <ContentMain>

      </ContentMain>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import RecordsNavigation from '@/views/records/partials/Navigation.vue';

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const filters = ref({
  searchQuery: '',
  sortOrder: 'name',
  selectedCategories: [],
  selectedRegisters: []
});

const form = ref({
  title: '',
});

const submit = () => {

};

onMounted(async () => {

});
</script>

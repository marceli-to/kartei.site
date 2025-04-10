<template>
  <div class="flex flex-grow w-full">
    <div class="flex mt-106 min-h-full w-full">
      <ContentNavigation>
        <div class="flex flex-col gap-y-48">
          <div class="flex flex-col gap-y-20">
            <InputGroup>
              <InputSearch
                v-model="searchQuery"
                placeholder="Suche"
                aria-label="Suche" />
            </InputGroup>
            <InputGroup>
              <InputLabel label="Sortierfolge" id="sort_order" />
              <InputSelect
                id="sort_order"
                v-model="sortOrder"
                :options="sortOrderOptions"
                variant="box"
              />
            </InputGroup>
            <InputGroup>
              <InputLabel label="Darstellung" id="layout" />
              <InputSelect
                id="layout"
                v-model="layout"
                :options="layoutOptions"
                variant="box"
              />
            </InputGroup>
          </div>
          <div class="flex flex-col gap-y-40">
            <InputGroup class="relative">
              <Action 
                label="Erstellen" 
                variant="box"
                :to="{ name: 'archiveSettings' }"
                :icon="{ name: 'Plus', position: 'right' }" />
                <template v-if="!hasArchive">
                  <InfoBox 
                    v-if="infoBox.isActive"
                    class="absolute !top-0 !-right-16 !w-auto min-h-default !py-0 flex items-center"
                    :closable="false">
                    <InfoCreateArchive />
                  </InfoBox>
                </template>
            </InputGroup>
            <InputGroup>
              <Action 
                label="Duplizieren" 
                variant="box"
                :icon="{ name: 'Plus', position: 'right' }" />
            </InputGroup>
          </div>
        </div>
      </ContentNavigation>
      <ContentMain>
        <div 
          v-if="archives.length > 0"
          class="flex flex-wrap gap-x-16 gap-y-32">
          <Teaser 
            :archive="archive" 
            class="w-full md:w-[calc(50%_-_8px)] lg:w-[calc(33.333%_-_(32px/3))] 2xl:w-[calc(25%_-_12px)] shrink-0"
            v-for="archive in archives" :key="archive.uuid" />
        </div>
      </ContentMain>
    </div>
  </div>
</template>
<script setup>
import { ref, toRef, onMounted } from 'vue';
import { useInfoBox } from '@/components/infobox/composables/useInfoBox';
import { usePageTitle } from '@/composables/usePageTitle';
import { useUserStore } from '@/stores/user';
import { getByUser } from '@/services/api/archive';
import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import InputSearch from '@/components/forms/Search.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputGroup from '@/components/forms/Group.vue';
import Action from '@/components/buttons/Action.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import InfoCreateArchive from '@/views/archives/partials/CreateArchiveInfo.vue';
import Teaser from '@/views/archives/partials/Teaser.vue';

const userStore = useUserStore();
const { setTitle } = usePageTitle();
setTitle('Meine Karteien');

const searchQuery = ref('');
const sortOrder = ref('name');
const layout = ref('list');
const hasArchive = ref(true);
const archives = ref([]);

const sortOrderOptions = [
  { value: 'name', label: 'Name' },
  { value: 'date', label: 'Datum' },
  { value: 'created_at', label: 'Erstellt' },
  { value: 'updated_at', label: 'Aktualisiert' },
];

const layoutOptions = [
  { value: 'list', label: 'Liste' },
  { value: 'grid', label: 'Gitter' },
];

const infoBox = useInfoBox({
  isActive: toRef(true),
  condition: hasArchive
});

onMounted(async () => {
  try {
    archives.value = await getByUser(userStore.user.uuid);
    hasArchive.value = archives.value.length > 0;
  }
  catch (error) {
    console.error(error);
  }
});
</script>
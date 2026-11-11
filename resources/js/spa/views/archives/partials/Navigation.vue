<template>
  <!-- Sidebar of "Meine Karteien" and "Duplizieren": Suchen, Erstellen, Duplizieren, Sortierfolge -->
  <div class="flex flex-col gap-y-20">
    <div class="flex flex-col gap-y-8">
      <InputGroup>
        <InputSearch
          v-model="search"
          placeholder="Suchen"
          aria-label="Suchen" />
      </InputGroup>
      <template v-if="userStore.can('create.archive')">
        <InputGroup class="relative">
          <Action
            label="Erstellen"
            variant="box"
            :to="{ name: 'archiveSettings' }"
            :icon="{ name: 'Plus', position: 'right' }" />
          <InfoBox
            v-if="showCreateInfo"
            class="absolute top-0! -right-16! flex min-h-default w-auto! items-center py-0!"
            :closable="false">
            <InfoCreateArchive />
          </InfoBox>
        </InputGroup>
        <InputGroup>
          <Action
            label="Duplizieren"
            variant="box"
            :to="{ name: 'archivesDuplicate' }"
            :classes="active === 'duplicate' ? 'border-black theme-color' : ''"
            :icon="{ name: 'duplicate', variant: 'small', position: 'right' }" />
        </InputGroup>
      </template>
    </div>
    <InputGroup>
      <InputLabel
        id="sort_order"
        label="Sortierfolge" />
      <InputSelect
        id="sort_order"
        v-model="sort"
        :options="sortOrderOptions"
        variant="box" />
    </InputGroup>
  </div>
</template>

<script setup>
import { useUserStore } from '@/stores/user';
import InputSearch from '@/components/forms/Search.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputGroup from '@/components/forms/Group.vue';
import Action from '@/components/buttons/Action.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import InfoCreateArchive from '@/views/archives/partials/CreateArchiveInfo.vue';

defineProps({
  // Highlighted entry: "duplicate" on the Duplizieren page
  active: {
    type: String,
    default: null,
  },
  // Points to "Erstellen" while there is no archive yet
  showCreateInfo: {
    type: Boolean,
    default: false,
  },
});

const search = defineModel('search', { type: String, default: '' });
const sort = defineModel('sort', { type: String, default: 'name' });

const userStore = useUserStore();

const sortOrderOptions = [
  { value: 'name', label: 'Name' },
  { value: 'date', label: 'Datum' },
  { value: 'created_at', label: 'Erstellt' },
  { value: 'updated_at', label: 'Aktualisiert' },
];
</script>

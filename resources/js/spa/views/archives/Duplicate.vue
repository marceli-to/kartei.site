<template>
  <!--
    "Kartei duplizieren" (Stackr #151, KAR0001_Desktop_261111 p. 12): the user's own archives as radio rows
    in a 3-of-10 column with a line on its right, the button, the info text, and "Abbrechen" at the bottom.
    The copy takes basic information, structure, card template, tags and members, not the records or the
    image; afterwards its Voreinstellungen open.
  -->
  <div class="relative flex w-full grow">
    <Actions>
      <router-link
        :to="{ name: 'archives' }"
        class="inline-flex items-center gap-x-12"
        aria-label="Zurück zu Meine Karteien">
        <Icon
          name="chevron-left"
          variant="small-bold" />
        <span class="font-muoto-medium leading-none">Meine Karteien</span>
      </router-link>
      <template #main>
        <h2 class="font-muoto-medium leading-none">Kartei duplizieren</h2>
      </template>
    </Actions>
    <div class="mt-106 flex min-h-full w-full">
      <ContentNavigation>
        <Navigation
          v-model:search="searchQuery"
          v-model:sort="sortOrder"
          active="duplicate" />
      </ContentNavigation>
      <ContentMain class="pb-0!">
        <div class="grid h-full grid-cols-1 gap-x-16 lg:grid-cols-10">
          <div
            class="relative flex flex-col pb-8 lg:col-span-3 lg:after:absolute lg:after:inset-y-0 lg:after:-right-8 lg:after:w-1 lg:after:bg-graphite">
            <template v-if="!isLoading">
              <template v-if="archives.length">
                <InputRadioGroup
                  v-if="filteredArchives.length"
                  v-model="selected"
                  name="archive"
                  legend="Kartei"
                  variant="reduced"
                  :options="filteredArchives" />
                <p
                  v-else
                  class="text-graphite">
                  Keine Ergebnisse gefunden.
                </p>

                <div class="mt-40 flex flex-col gap-y-16">
                  <Action
                    type="button"
                    label="Ausgewählte Kartei duplizieren"
                    variant="box"
                    classes="border-black"
                    :disabled="!selected || isSaving"
                    @click="duplicate" />
                  <div class="px-8 text-xs italic">
                    <p class="mb-0!">Die folgenden Voreinstellungen werden übernommen:</p>
                    <ul>
                      <li
                        v-for="item in TAKEN_OVER"
                        :key="item">
                        –&ensp;{{ item }}
                      </li>
                    </ul>
                    <p class="mt-16">
                      Die Karten werden nicht dupliziert. Ihr Duplikat wird angelegt als: «{{ copyName }}».
                    </p>
                  </div>
                </div>
              </template>
              <p
                v-else
                class="text-graphite">
                Sie haben noch keine eigene Kartei, die dupliziert werden kann.
              </p>
            </template>

            <ButtonGroup class="mt-auto pt-40">
              <AppButton
                type="button"
                label="Abbrechen"
                @click="router.push({ name: 'archives' })" />
            </ButtonGroup>
          </div>
        </div>
      </ContentMain>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { usePageTitle } from '@/composables/usePageTitle';
import { useUserStore } from '@/stores/user';
import { useToastStore } from '@/stores/toast';
import { getByUser, duplicateArchive } from '@/services/api/archive';
import { notifyError } from '@/services/api/error';
import ContentNavigation from '@/components/layout/ContentNavigation.vue';
import ContentMain from '@/components/layout/ContentMain.vue';
import Actions from '@/views/records/partials/Actions.vue';
import Navigation from '@/views/archives/partials/Navigation.vue';
import InputRadioGroup from '@/components/forms/RadioGroup.vue';
import Action from '@/components/buttons/Action.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import AppButton from '@/components/buttons/Button.vue';
import Icon from '@/components/icons/Icon.vue';

const TAKEN_OVER = ['Basisinformationen', 'Struktur', 'Kartenvorlage', 'Tags', 'Benutzer/innen'];

const router = useRouter();
const userStore = useUserStore();
const { setTitle } = usePageTitle();
setTitle('Duplizieren');

const searchQuery = ref('');
const sortOrder = ref('name');
const isLoading = ref(true);
const isSaving = ref(false);
const archives = ref([]);
const selected = ref('');
// Names of all the user's archives, for the next free "(Kopie N)"
const allNames = ref([]);

const filteredArchives = computed(() => {
  const query = searchQuery.value.trim().toLowerCase();
  return archives.value
    .filter((archive) => !query || archive.label.toLowerCase().includes(query))
    .sort((a, b) => a.label.localeCompare(b.label, 'de'));
});

// Same rule as the API: "Name (Kopie)", or the next free "(Kopie N)"; a copy of a copy counts on
const copyName = computed(() => {
  const archive = archives.value.find((item) => item.value === selected.value);
  if (!archive) return '';
  const base = archive.label.replace(/ \(Kopie(?: \d+)?\)$/, '');
  const taken = new Set(allNames.value);
  let candidate = `${base} (Kopie)`;
  for (let n = 2; taken.has(candidate); n++) candidate = `${base} (Kopie ${n})`;
  return candidate;
});

const duplicate = async () => {
  if (!selected.value || isSaving.value) return;
  isSaving.value = true;
  try {
    const copy = await duplicateArchive(selected.value);
    await userStore.fetchPermissions();
    useToastStore().show(`Kartei dupliziert: «${copy.name}»`, 'success');
    router.push({ name: 'archiveSettings', params: { uuid: copy.uuid } });
  } catch (error) {
    notifyError(error, 'Die Kartei konnte nicht dupliziert werden.');
  } finally {
    isSaving.value = false;
  }
};

onMounted(async () => {
  try {
    await userStore.initialize();
    const all = await getByUser(userStore.user.uuid);
    allNames.value = all.map((archive) => archive.name);
    // Only the user's own archives (the copy takes their members along)
    archives.value = all
      .filter((archive) => userStore.canArchive(archive.uuid, 'duplicate'))
      .map((archive) => ({ value: archive.uuid, label: archive.name }));
    selected.value = filteredArchives.value[0]?.value || '';
  } catch (error) {
    notifyError(error, 'Die Karteien konnten nicht geladen werden.');
  } finally {
    isLoading.value = false;
  }
});
</script>

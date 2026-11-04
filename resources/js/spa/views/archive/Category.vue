<template>
  <Slide>
    <form 
      class="w-full h-full flex flex-col justify-between pb-24 relative before:absolute before:inset-y-0 before:left-1/4 before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-3/4 after:w-1 after:bg-graphite after:-z-1"
      @submit.prevent="handleSubmit"
      v-if="!isLoading">

      <!-- Info box -->
      <template v-if="isInfoOpen">
        <InfoBox class="!-top-0 !-right-16 w-4/12">
          <InfoStructure />
        </InfoBox>
      </template>

      <!-- Categories and Registers -->
      <div class="w-full">
        <div class="flex gap-x-16 mb-32">
          <div class="w-3/12">
            <button 
              type="button" 
              class="min-h-default border-y border-y-graphite w-full flex items-center justify-start"
              aria-label="Ordnungsstruktur einstellen"
              @click="showStructureDialog">
              <Icon name="settings" />
            </button>
          </div>
          <div class="w-6/12">
            <Action 
              type="button"
              label="Kategorie" 
              classes="!font-muoto-medium"
              :icon="{ name: 'Plus', variant: 'small-bold', position: 'center' }"
              @click="addCategory" />
          </div>
          <div class="w-3/12">
            <button 
              type="button" 
              class="min-h-default border-y border-y-graphite w-full flex items-center justify-start"
              aria-label="Ordnungsstruktur einstellen"
              @click="showStructureDialog">
              <Icon name="settings" />
            </button>
          </div>
        </div>

        <div class="flex gap-x-17 mb-4">
          <InputLabel label="Nr." class="w-3/12 !mb-0" />
          <InputLabel label="Titel" class="w-6/12 !mb-0" />
          <InputLabel label="Kürzel" class="w-3/12 !mb-0" />
        </div>

        <draggable 
          v-model="categories" 
          group="categories" 
          :item-key="rowKey" 
          class="flex flex-col" 
          handle=".drag-handle">
          <template #item="{ element: category, index: cIdx }">
            <div>
              <StructureRow
                class="mb-32"
                v-model:name="category.name"
                v-model:custom-id="category.custom_id"
                label="Kategorie"
                :position="cIdx + 1"
                :number="categoryNumber(cIdx)"
                :placeholder="`Kategorie ${cIdx + 1}`"
                :readonly="idType === 'auto'"
                @blur="applyAutoId(category)"
                @remove="removeCategory(cIdx)" />

              <draggable 
                v-model="category.registers" 
                group="registers" 
                :item-key="rowKey" 
                :clone="cloneRegister" 
                class="flex flex-col" 
                handle=".drag-handle">
                <template #item="{ element: register, index: rIdx }">
                  <StructureRow
                    class="mb-8 last:mb-32"
                    v-model:name="register.name"
                    v-model:custom-id="register.custom_id"
                    label="Register"
                    :position="rIdx + 1"
                    :number="registerNumber(cIdx, rIdx)"
                    :readonly="idType === 'auto'"
                    @blur="applyAutoId(register)"
                    @remove="removeRegister(cIdx, rIdx)" />
                </template>
              </draggable>
            </div>
          </template>
        </draggable>

        <div class="flex gap-x-17 mb-8">
          <div class="w-3/12">&nbsp;</div>
          <div class="w-6/12">
            <Action 
              type="button"
              label="Register"
              :icon="{ name: 'Plus', variant: 'small', position: 'center' }"
              @click="addRegisterToLastCategory" />
          </div>
          <div class="w-3/12">&nbsp;</div>
        </div>
      </div>

      <!-- Buttons -->
      <ButtonGroup class="relative z-10">
        <AppButton type="submit" label="Speichern" :loading="isSaving" />
      </ButtonGroup>
    </form>
  </Slide>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import draggable from 'vuedraggable';
import { useToastStore } from '@/stores/toast';
import { useDialogStore } from '@/stores/dialog';
import { useInfoBox } from '@/composables/useInfoBox';
import { useStructureForm } from '@/views/archive/composables/useStructureForm';
import { deleteCategory, deleteRegister } from '@/services/api/category';
import { notifyError } from '@/services/api/error';
import Slide from '@/components/slider/Slide.vue';
import Action from '@/components/buttons/Action.vue';
import InputLabel from '@/components/forms/Label.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import StructureRow from '@/views/archive/partials/StructureRow.vue';
import StructureDialog from '@/views/archive/partials/StructureDialog.vue';
import InfoStructure from '@/views/archive/partials/StructureInfo.vue';
import Icon from '@/components/icons/Icon.vue';
import AppButton from '@/components/buttons/Button.vue';

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const toast = useToastStore();
const dialogStore = useDialogStore();
const route = useRoute();
const uuid = route.params.uuid || null;

const {
  categories,
  numeralsCategory,
  numeralsRegister,
  idType,
  hasCategories,
  rowKey,
  categoryNumber,
  registerNumber,
  load,
  save,
  applyAutoId,
  addCategory,
  addRegisterToLastCategory,
} = useStructureForm(uuid);

// Loading from the first render, so the info box doesn't flash open before the fetch
const isLoading = ref(!!uuid);
const isSaving = ref(false);

// Opens by itself while the archive has no categories yet
const { isOpen: isInfoOpen } = useInfoBox({
  active: () => props.isActive,
  auto: () => !isLoading.value && !hasCategories.value,
});

onMounted(async () => {
  if (!uuid) return;
  try {
    await load();
  }
  catch (error) {
    notifyError(error, 'Die Struktur konnte nicht geladen werden.');
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  isSaving.value = true;
  try {
    if (await save()) {
      toast.show('Ordnung erfolgreich gespeichert', 'success');
    }
    else {
      toast.show('Fehler: Bitte füllen Sie alle Felder aus (Nummer, Titel, Kürzel)', 'error');
    }
  }
  catch (error) {
    notifyError(error, 'Fehler beim Speichern der Ordnung');
  }
  finally {
    isSaving.value = false;
  }
};

// Unsaved rows go at once; saved ones after a confirmation
const removeCategory = (cIdx) => {
  const category = categories.value[cIdx];
  if (!category.uuid) {
    categories.value.splice(cIdx, 1);
    return;
  }

  dialogStore.confirmDelete({
    title: 'Kategorie löschen',
    message: `Möchten Sie die Kategorie "${category.name}" wirklich löschen? Alle zugehörigen Register werden ebenfalls gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.`,
    action: () => deleteCategory(category.uuid),
    then: () => categories.value.splice(categories.value.indexOf(category), 1),
    success: 'Kategorie erfolgreich gelöscht',
    failure: 'Fehler beim Löschen der Kategorie',
  });
};

const removeRegister = (cIdx, rIdx) => {
  const category = categories.value[cIdx];
  const register = category.registers[rIdx];
  if (!register.uuid) {
    category.registers.splice(rIdx, 1);
    return;
  }

  dialogStore.confirmDelete({
    title: 'Register löschen',
    message: `Möchten Sie das Register "${register.name}" wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.`,
    action: () => deleteRegister(register.uuid),
    then: () => category.registers.splice(category.registers.indexOf(register), 1),
    success: 'Register erfolgreich gelöscht',
    failure: 'Fehler beim Löschen des Registers',
  });
};

const cloneRegister = (original) => ({ ...original });

const showStructureDialog = () => {
  dialogStore.show({
    component: StructureDialog,
    hideDefaultActions: true,
    size: 'xlarge',
    props: {
      categoryType: numeralsCategory.value,
      registerType: numeralsRegister.value,
      idType: idType.value,
      onConfirm: ({ category, register, idType: newIdType }) => {
        numeralsCategory.value = category;
        numeralsRegister.value = register;
        idType.value = newIdType;
        dialogStore.hide();
      },
      onCancel: () => {
        dialogStore.hide();
      }
    }
  });
};
</script>

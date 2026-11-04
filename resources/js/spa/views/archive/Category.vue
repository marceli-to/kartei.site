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
              <IconSettings />
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
              <IconSettings />
            </button>
          </div>
        </div>

        <div class="flex gap-x-17 mb-4">
          <InputLabel label="Nr." class="w-3/12 !mb-0" />
          <InputLabel label="Titel" class="w-6/12 !mb-0" />
          <InputLabel label="Kürzel" class="w-3/12 !mb-0" />
        </div>

        <draggable 
          v-model="form.categories" 
          group="categories" 
          :item-key="rowKey" 
          class="flex flex-col" 
          handle=".drag-handle">
          <template #item="{ element: category, index: cIdx }">
            <div>
              <div class="flex gap-x-17 mb-32 items-center drag-handle cursor-move">
                <div class="w-3/12">
                  <InputStatic>{{ formatNumber(cIdx, numerals_category, true) }}</InputStatic>
                </div>
                <div class="w-6/12 relative">
                  <InputText
                    v-model="category.name"
                    :placeholder="`Kategorie ${cIdx + 1}`"
                    @blur="applyAutoId(category)"
                  />
                  <button 
                    type="button" 
                    class="absolute right-8 top-12"
                    :aria-label="`Kategorie ${cIdx + 1} entfernen`"
                    @click="removeCategory(cIdx)"
                    >
                    <IconCross variant="small" />
                  </button>
                </div>
                <div class="w-3/12">
                  <InputText
                    v-model="category.custom_id"
                    :readonly="id_type === 'auto'"
                  />
                </div>
              </div>

              <draggable 
                v-model="category.registers" 
                group="registers" 
                :item-key="rowKey" 
                :clone="cloneRegister" 
                class="flex flex-col" 
                handle=".drag-handle">
                <template #item="{ element: register, index: rIdx }">
                  <div class="flex gap-x-17 mb-8 last:mb-32 items-center drag-handle cursor-move">
                    <div class="w-3/12">
                      <InputStatic>{{ formatNumber(cIdx, numerals_category, true) + formatNumber(rIdx, numerals_register, false) }}</InputStatic>
                    </div>
                    <div class="w-6/12 relative">
                      <InputText v-model="register.name" @blur="applyAutoId(register)" />
                      <button 
                        type="button" 
                        class="absolute right-8 top-12"
                        :aria-label="`Register ${rIdx + 1} entfernen`"
                        @click="removeRegister(cIdx, rIdx)"
                        >
                        <IconCross variant="small" />
                      </button>
                    </div>
                    <div class="w-3/12">
                      <InputText
                        v-model="register.custom_id"
                        :readonly="id_type === 'auto'"
                      />
                    </div>
                  </div>
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
        <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      </ButtonGroup>
    </form>
  </Slide>
</template>

<script setup>
import { ref, onMounted, toRaw } from 'vue';

import { useRoute } from 'vue-router';
import { useToastStore } from '@/stores/toast';
import { useDialogStore } from '@/stores/dialog';
import { useInfoBox } from '@/composables/useInfoBox';
import { getCategories, storeCategory, deleteCategory, deleteRegister } from '@/services/api/category';
import draggable from 'vuedraggable';
import IconSettings from '@/components/icons/Settings.vue';
import IconCross from '@/components/icons/Cross.vue';
import Slide from '@/components/slider/Slide.vue';
import Action from '@/components/buttons/Action.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import StructureDialog from '@/views/archive/partials/StructureDialog.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';
import InfoStructure from '@/views/archive/partials/StructureInfo.vue';
import { notifyError } from '@/services/api/error';
import { formatNumber } from '@/utils/numbering';

// Stable drag keys per row object. custom_id can be empty or duplicated
// ("Bilder" and "Bibliothek" both become "BI") and changes while typing.
const rowKeys = new WeakMap();
let nextRowKey = 1;
const rowKey = (row) => {
  const raw = toRaw(row);
  if (!rowKeys.has(raw)) rowKeys.set(raw, nextRowKey++);
  return rowKeys.get(raw);
};

const dialogStore = useDialogStore();

const toast = useToastStore();
const isSaving = ref(false);
const route = useRoute();
const uuid = ref(route.params.uuid || null);

// Loading from the first render, so the info box doesn't flash open before the fetch
const isLoading = ref(!!uuid.value);
const hasCategories = ref(false);

const numerals_category = ref('decimal'); // decimal, roman, alpha
const numerals_register = ref('decimal'); // decimal, roman, alpha
const id_type = ref('auto'); // auto, manual

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

// Opens by itself while the archive has no categories yet
const { isOpen: isInfoOpen } = useInfoBox({
  active: () => props.isActive,
  auto: () => !isLoading.value && !hasCategories.value,
});

const form = ref({
  numerals_category: numerals_category.value,
  numerals_register: numerals_register.value,
  custom_id_type: id_type.value,
  categories: [
    {
      custom_id: '',
      name: '',
      uuid: null,
      registers: []
    }
  ]
});

onMounted(async () => {
  if (uuid.value) {
    await fetchStructure();
  }
});

const fetchStructure = async () => {
  try {
    const response = await getCategories(uuid.value);
    const mapped = Array.isArray(response.data) ? 
      response.data.map(category => ({
          name: category.name,
          custom_id: category.custom_id ?? '',
          uuid: category.uuid,
          registers: (category.registers || []).map(register => ({
            name: register.name,
            custom_id: register.custom_id ?? '',
            uuid: register.uuid
          }))
        }))
      : [];

    form.value.categories = mapped.length > 0 ? mapped : [emptyCategory()];
    hasCategories.value = mapped.length > 0;

    // Registers store their own numeral type; read it from the first register there is
    const categories = Array.isArray(response.data) ? response.data : [];
    const firstRegister = categories.flatMap(category => category.registers || [])[0];
    numerals_category.value = categories[0]?.numeral_type ?? 'decimal';
    numerals_register.value = firstRegister?.numeral_type ?? numerals_category.value;
    id_type.value = categories[0]?.custom_id_type ?? 'auto';
  } 
  catch (error) {
    notifyError(error, 'Die Struktur konnte nicht geladen werden.');
  } 
  finally {
    isLoading.value = false;
  }
};

const handleSubmit = async () => {
  isSaving.value = true;

  try {
    cleanForm();

    // Enter submits without a blur, so auto IDs may not be derived yet
    form.value.categories.forEach(category => {
      applyAutoId(category);
      category.registers.forEach(applyAutoId);
    });

    const payload = form.value.categories.map((category, cIdx) => {
      const categoryNumber = formatNumber(cIdx, numerals_category.value);
      return {
        numeral_type: numerals_category.value,
        custom_id_type: id_type.value,
        uuid: category.uuid,
        number: categoryNumber.toUpperCase(),
        name: category.name,
        custom_id: category.custom_id,
        order: cIdx,
        registers: category.registers.map((register, rIdx) => ({
          numeral_type: numerals_register.value,
          custom_id_type: id_type.value,
          uuid: register.uuid,
          number: `${categoryNumber.toUpperCase()}.${formatNumber(rIdx, numerals_register.value)}`,
          name: register.name,
          custom_id: register.custom_id,
          order: rIdx
        }))
      };
    });

    if (!validatePayload(payload)) return;

    const response = await storeCategory(uuid.value, payload);
    updateForm(response.data);
    toast.show('Ordnung erfolgreich gespeichert', 'success');
  } 
  catch (error) {
    notifyError(error, 'Fehler beim Speichern der Ordnung');
  } 
  finally {
    isSaving.value = false;
  }
};

const validatePayload = (payload) => {
  let isValid = true;

  payload.forEach(category => {
    if (!category.custom_id || !category.number || !category.name) {
      isValid = false;
    }
    category.registers.forEach(register => {
      if (!register.custom_id || !register.number || !register.name) {
        isValid = false;
      }
    });
  });

  if (!isValid) {
    toast.show('Fehler: Bitte füllen Sie alle Felder aus (Nummer, Titel, Kürzel)', 'error');
  }

  return isValid;
};

const updateForm = (data) => {
  data.forEach((catRes, cIdx) => {
    const category = form.value.categories[cIdx];
    if (category) {
      category.uuid = catRes.uuid;
      category.custom_id = catRes.custom_id;

      catRes.registers?.forEach((regRes, rIdx) => {
        const register = category.registers[rIdx];
        if (register) {
          register.uuid = regRes.uuid;
          register.custom_id = regRes.custom_id;
        }
      });
    }
  });

  hasCategories.value = data.length > 0;

  // add an empty category if none exist
  if (form.value.categories.length === 0) {
    form.value.categories.push(emptyCategory());
  }

};

const cleanForm = () => {
  form.value.categories = form.value.categories.filter(category => {
    category.registers = category.registers.filter(register =>
      register.name || register.custom_id || register.uuid
    );
    return category.name || category.custom_id || category.uuid || category.registers.length > 0;
  });
};

const emptyCategory = () => ({
  name: '',
  custom_id: '',
  uuid: null,
  registers: []
});

const addCategory = () => {
  form.value.categories.push(
    emptyCategory()
  );
};

const removeCategory = async (cIdx) => {
  const category = form.value.categories[cIdx];
  
  // If category doesn't have a UUID, it's not saved yet, just remove locally
  if (!category.uuid) {
    form.value.categories.splice(cIdx, 1);
    return;
  }

  dialogStore.confirmDelete({
    title: 'Kategorie löschen',
    message: `Möchten Sie die Kategorie "${category.name}" wirklich löschen? Alle zugehörigen Register werden ebenfalls gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.`,
    action: () => deleteCategory(category.uuid),
    then: () => form.value.categories.splice(cIdx, 1),
    success: 'Kategorie erfolgreich gelöscht',
    failure: 'Fehler beim Löschen der Kategorie',
  });
};

const removeRegister = async (cIdx, rIdx) => {
  const category = form.value.categories[cIdx];
  const register = category.registers[rIdx];
  
  // If register doesn't have a UUID, it's not saved yet, just remove locally
  if (!register.uuid) {
    category.registers.splice(rIdx, 1);
    return;
  }

  dialogStore.confirmDelete({
    title: 'Register löschen',
    message: `Möchten Sie das Register "${register.name}" wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.`,
    action: () => deleteRegister(register.uuid),
    then: () => category.registers.splice(rIdx, 1),
    success: 'Register erfolgreich gelöscht',
    failure: 'Fehler beim Löschen des Registers',
  });
};

const addRegisterToLastCategory = () => {
  if (form.value.categories.length === 0) return;
  const category = form.value.categories[form.value.categories.length - 1];
  category.registers.push({
    name: '',
    custom_id: '',
    uuid: null
  });
};

// In auto mode the ID is the first two letters of the name (category or register)
const applyAutoId = (item) => {
  if (id_type.value !== 'auto') return;
  item.custom_id = item.name.slice(0, 2).toUpperCase();
};

const cloneRegister = (original) => ({ ...original });

const showStructureDialog = () => {
  dialogStore.show({
    component: StructureDialog,
    hideDefaultActions: true,
    size: 'xlarge',
    props: {
      numerals_category_type: numerals_category.value,
      numerals_register_type: numerals_register.value,
      id_type: id_type.value,
      onConfirm: ({ category, register, idType }) => {
        numerals_category.value = category;
        numerals_register.value = register;
        id_type.value = idType;
        dialogStore.hide();
      },
      onCancel: () => {
        dialogStore.hide();
      }
    }
  });
}
</script>
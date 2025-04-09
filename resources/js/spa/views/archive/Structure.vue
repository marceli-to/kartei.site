<template>
  <Slide>
    <form 
      class="w-full h-full flex flex-col justify-between pb-24 relative before:absolute before:inset-y-0 before:left-1/4 before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-3/4 after:w-1 after:bg-graphite after:-z-1"
      @submit.prevent="handleSubmit"
      v-if="!isLoading">

      <!-- Info box -->
      <template v-if="(!hasStructure && isActive) || (infoBox.isActive && isActive)">
        <InfoBox class="!top-0 !-right-16 w-4/12">
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
              @click="showStructureDialog">
              <IconSettings />
            </button>
          </div>
        </div>

        <div class="flex gap-x-17 mx-8 mb-4">
          <InputLabel label="Nr." class="w-3/12 !mb-0" />
          <InputLabel label="Titel" class="w-6/12 !mb-0" />
          <InputLabel label="Kürzel" class="w-3/12 !mb-0" />
        </div>

        <draggable v-model="form.categories" group="categories" item-key="custom_id" class="flex flex-col" handle=".drag-handle">
          <template #item="{ element: category, index: cIdx }">
            <div>
              <div class="flex gap-x-17 mb-32 items-center drag-handle cursor-move">
                <div class="w-3/12">
                  <InputStatic>{{ formatNumber(cIdx, numerals_category, true) }}</InputStatic>
                </div>
                <div class="w-6/12">
                  <InputText
                    v-model="category.title"
                    :placeholder="`Kategorie ${cIdx + 1}`"
                    @blur="onCategoryTitleBlur(category)"
                  />
                </div>
                <div class="w-3/12">
                  <InputText
                    :model-value="category._localId"
                    @update:model-value="val => category._localId = val"
                    @blur="() => onCategoryIdBlur(category)"
                    :readonly="id_type === 'auto'"
                  />
                </div>
              </div>

              <draggable v-model="category.registers" group="registers" item-key="custom_id" :clone="cloneRegister" class="flex flex-col" handle=".drag-handle">
                <template #item="{ element: register, index: rIdx }">
                  <div class="flex gap-x-17 mb-8 last:mb-32 items-center drag-handle cursor-move">
                    <div class="w-3/12">
                      <InputStatic>{{ formatNumber(cIdx, numerals_category, true) + formatNumber(rIdx, numerals_register, false) }}</InputStatic>
                    </div>
                    <div class="w-6/12">
                      <InputText v-model="register.title" @blur="onRegisterTitleBlur(register)" />
                    </div>
                    <div class="w-3/12">
                      <InputText
                        :model-value="register._localId"
                        @update:model-value="val => register._localId = val"
                        @blur="() => onRegisterIdBlur(register)"
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
      <ButtonGroup class="mx-8 relative z-10">
        <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
        <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
      </ButtonGroup>
    </form>
  </Slide>
</template>

<script setup>
import { ref, toRef, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useToastStore } from '@/components/toast/stores/toast';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { useInfoBox } from '@/components/infobox/composables/useInfoBox';
import { getStructure, storeStructure } from '@/services/api/archiveStructure';
import draggable from 'vuedraggable';
import IconSettings from '@/components/icons/Settings.vue';
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

const dialogStore = useDialogStore();

const toast = useToastStore();
const isSaving = ref(false);
const isLoading = ref(false);
const hasStructure = ref(false);

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const numerals_category = ref('decimal'); // decimal, roman, alpha
const numerals_register = ref('decimal'); // decimal, roman, alpha
const id_type = ref('auto'); // auto, manual

const infoBox = useInfoBox({
  isActive: toRef(true),
  condition: hasStructure
});

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const form = ref({
  categories: [
    {
      custom_id: '',
      _localId: '',
      title: '',
      uuid: null,
      registers: []
    }
  ]
});

onMounted(async () => {
  if (uuid.value) {
    isLoading.value = true;
    await fetchStructure();
  }
});

const fetchStructure = async () => {
  try {
    const response = await getStructure(uuid.value);
    const mapped = Array.isArray(response.data) ? 
      response.data.map(category => ({
          title: category.title,
          custom_id: category.custom_id ?? '',
          _localId: category.custom_id ?? '',
          uuid: category.uuid,
          registers: (category.registers || []).map(register => ({
            title: register.title,
            custom_id: register.custom_id ?? '',
            _localId: register.custom_id ?? '',
            uuid: register.uuid
          }))
        }))
      : [];

    form.value.categories = mapped.length > 0 ? mapped : [emptyCategory()];
    hasStructure.value = mapped.length > 0 || false;

  } 
  catch (error) {
    console.error(error);
    toast.show('Fehler beim Laden der Ordnung', 'error');
  } 
  finally {
    isLoading.value = false;
  }
};

const handleSubmit = async () => {
  isSaving.value = true;

  try {
    cleanForm();

    const payload = form.value.categories.map((category, cIdx) => {
      const categoryNumber = formatNumber(cIdx, numerals_category.value);
      return {
        uuid: category.uuid,
        number: categoryNumber,
        title: category.title,
        custom_id: category.custom_id,
        order: cIdx,
        registers: category.registers.map((register, rIdx) => ({
          uuid: register.uuid,
          number: `${categoryNumber}${formatNumber(rIdx, numerals_register.value)}`,
          title: register.title,
          custom_id: register.custom_id,
          order: rIdx
        }))
      };
    });

    if (!validatePayload(payload)) {
      isSaving.value = false;
      return;
    }

    const response = await storeStructure(uuid.value, payload);
    updateForm(response.data);
    toast.show('Ordnung erfolgreich gespeichert', 'success');
  } 
  catch (error) {
    toast.show('Fehler beim Speichern der Ordnung', 'error');
    console.error(error);
  } 
  finally {
    isSaving.value = false;
  }
};

const validatePayload = (payload) => {
  let isValid = true;

  payload.forEach(category => {
    if (!category.custom_id || !category.number || !category.title) {
      isValid = false;
    }
    category.registers.forEach(register => {
      if (!register.custom_id || !register.number || !register.title) {
        isValid = false;
      }
    });
  });

  if (!isValid) {
    toast.show('Fehler: Bitte fülle alle Felder aus (Nummer, Titel, Kürzel)', 'error');
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
};

const cleanForm = () => {
  form.value.categories = form.value.categories.filter(category => {
    category.registers = category.registers.filter(register =>
      register.title || register.custom_id || register.uuid
    );
    return category.title || category.custom_id || category.uuid || category.registers.length > 0;
  });
};

const emptyCategory = () => ({
  title: '',
  custom_id: '',
  _localId: '',
  uuid: null,
  registers: []
});

const addCategory = () => {
  form.value.categories.push(
    emptyCategory()
  );
};

const addRegisterToLastCategory = () => {
  if (form.value.categories.length === 0) return;
  const category = form.value.categories[form.value.categories.length - 1];
  category.registers.push({
    title: '',
    custom_id: '',
    _localId: '',
    uuid: null
  });
};

const onCategoryTitleBlur = (category) => {
  if (id_type.value !== 'auto') return;
  const autoId = category.title.slice(0, 2).toUpperCase();
  category.custom_id = autoId;
  category._localId = autoId;
};

const onCategoryIdBlur = (category) => {
  if (id_type.value === 'manual') {
    category.custom_id = category._localId;
  }
};

const onRegisterTitleBlur = (register) => {
  if (id_type.value !== 'auto') return;
  const autoId = register.title.slice(0, 2).toUpperCase();
  register.custom_id = autoId;
  register._localId = autoId;
};

const onRegisterIdBlur = (register) => {
  if (id_type.value === 'manual') {
    register.custom_id = register._localId;
  }
};

const cloneRegister = (original) => ({ ...original });

const formatNumber = (index, type = 'decimal', isCategory = false) => {
  switch (type) {
    case 'alpha': {
      const char = String.fromCharCode(65 + index);
      return isCategory ? char + '.' : char.toLowerCase();
    }
    case 'roman':
      return isCategory ? toRoman(index + 1) + '.' : toRoman(index + 1);
    default:
      return isCategory ? `${index + 1}.` : `${index + 1}`;
  }
};

const toRoman = (num) => {
  const romans = [
    ['M', 1000], ['CM', 900], ['D', 500], ['CD', 400],
    ['C', 100], ['XC', 90], ['L', 50], ['XL', 40],
    ['X', 10], ['IX', 9], ['V', 5], ['IV', 4], ['I', 1]
  ];
  return romans.reduce((acc, [letter, value]) => {
    while (num >= value) {
      acc += letter;
      num -= value;
    }
    return acc;
  }, '');
};

const showStructureDialog = () => {
  dialogStore.show({
    component: StructureDialog,
    hideDefaultActions: true,
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
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
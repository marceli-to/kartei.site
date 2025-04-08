<template>
  <form 
    class="w-full h-full flex flex-col justify-between relative pb-40 before:absolute before:inset-y-0 before:left-[calc(25%_+_4px)] before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-[calc(75%_-_4px)] after:w-1 after:bg-graphite after:-z-1"
    @submit.prevent="handleSubmit"
    v-if="!isLoading">

    <!-- Categories and Registers -->
    <div class="w-full">
      <div class="flex gap-x-16 mx-8 mb-32">
        <div class="w-3/12">
          <button 
            type="button" 
            class="min-h-default border-y border-y-graphite  w-full flex items-center justify-start">
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
            class="min-h-default border-y border-y-graphite w-full flex items-center justify-start">
            <IconSettings />
          </button>
        </div>
      </div>

      <div class="flex gap-x-17 mx-8 mb-4">
        <InputLabel label="Nr." class="w-3/12 !mb-0" />
        <InputLabel label="Titel" class="w-6/12 !mb-0" />
        <InputLabel label="ID" class="w-3/12 !mb-0" />
      </div>

      <draggable 
        v-model="form.categories" 
        group="categories" 
        item-key="id" 
        class="flex flex-col"
        handle=".drag-handle">
        <template #item="{ element: category, index: cIdx }">
          <div>
            <div class="flex gap-x-17 mx-8 mb-32 items-center drag-handle cursor-move">
              <div class="w-3/12">
                <InputStatic>{{ formatNumber(cIdx, numerals_category) }}</InputStatic>
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

            <draggable 
              v-model="category.registers" 
              group="registers" 
              item-key="id" 
              :clone="cloneRegister" 
              class="flex flex-col"
              handle=".drag-handle">
              <template #item="{ element: register, index: rIdx }">
                <div class="flex gap-x-17 mx-8 mb-8 last:mb-32 items-center drag-handle cursor-move">
                  <div class="w-3/12">
                    <InputStatic>{{ `${formatNumber(cIdx, numerals_category)}${formatNumber(rIdx, numerals_register)}` }}</InputStatic>
                  </div>
                  <div class="w-6/12">
                    <InputText 
                      v-model="register.title"
                      @blur="onRegisterTitleBlur(register)" />
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

      <div class="flex gap-x-17 mx-8 mb-8">
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
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import draggable from 'vuedraggable';
import { useToastStore } from '@/components/toast/stores/toast';
import { getStructure, storeStructure } from '@/services/api/archiveStructure';
import IconSettings from '@/components/icons/Settings.vue';
import Action from '@/components/buttons/Action.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';

const toast = useToastStore();
const isSaving = ref(false);
const isLoading = ref(false);

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const numerals_category = ref('alpha'); // decimal, roman, alpha
const numerals_register = ref('decimal');
const id_type = ref('auto'); // auto, manual

const form = ref({
  categories: [
    {
      id: '',
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
    isLoading.value = true;
    const response = await getStructure(uuid.value);

    form.value.categories = response.data.map(category => ({
      title: category.title,
      custom_id: category.custom_id,
      _localId: category.custom_id,
      uuid: category.uuid,
      registers: category.registers.map(register => ({
        title: register.title,
        custom_id: register.custom_id,
        _localId: register.custom_id,
        uuid: register.uuid
      }))
    }));
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
    const payload = [];

    form.value.categories.forEach((category, cIdx) => {
      const categoryNumber = formatNumber(cIdx, numerals_category.value);
      const categoryItem = {
        number: categoryNumber,
        title: category.title,
        custom_id: category.id,
        order: cIdx,
        uuid: category.uuid,
        registers: category.registers.map((register, rIdx) => ({
          number: `${categoryNumber}${formatNumber(rIdx, numerals_register.value)}`,
          title: register.title,
          custom_id: register.id,
          order: rIdx,
          uuid: register.uuid
        })),
      };
      payload.push(categoryItem);
    });

    // Validate payload
    if (!validatePayload(payload)) {
      isSaving.value = false;
      return;
    }

    // Send to API
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
      register.title || register.id || register.uuid
    );
    return category.title || category.id || category.uuid || category.registers.length > 0;
  });
};

const addCategory = () => {
  const newCategory = {
    title: '',
    id: '',
    _localId: '',
    uuid: null,
    registers: []
  };
  form.value.categories.push(newCategory);
}

const onCategoryTitleBlur = (category) => {
  if (id_type.value !== 'auto') return;
  const autoId = category.title.slice(0, 2).toUpperCase();
  if (category.id !== autoId) {
    category.id = autoId;
    category._localId = autoId;
  }
}

const onCategoryIdBlur = (category) => {
  if (id_type.value === 'manual') {
    category.id = category._localId;
  }
}

const onRegisterTitleBlur = (register) => {
  if (id_type.value !== 'auto') return;
  const autoId = register.title.slice(0, 2).toUpperCase();
  if (register.id !== autoId) {
    register.id = autoId;
    register._localId = autoId;
  }
}

const onRegisterIdBlur = (register) => {
  if (id_type.value === 'manual') {
    register.id = register._localId;
  }
}

const addRegisterToLastCategory = () => {
  if (form.value.categories.length === 0) return;
  const lastCategoryIndex = form.value.categories.length - 1;
  const category = form.value.categories[lastCategoryIndex];
  category.registers.push({
    title: '',
    id: '',
    uuid: null,
    _localId: ''
  });
}

const cloneRegister = (original) => {
  return { ...original };
}

const formatNumber = (index, type = 'decimal') => {
  switch (type) {
    case 'alpha':
      return String.fromCharCode(65 + index).toLowerCase() + '.';
    case 'roman':
      return toRoman(index + 1) + '.';
    default:
      return `${index + 1}.`;
  }
}

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
  }, '')
}
</script>

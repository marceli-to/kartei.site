// Register ID auto-generation logic added

<template>
  <div class="w-full h-full flex flex-col justify-between relative pb-40 before:absolute before:inset-y-0 before:left-[calc(25%_+_4px)] before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-[calc(75%_-_4px)] after:w-1 after:bg-graphite after:-z-1">

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
            label="Register"
            :icon="{ name: 'Plus', variant: 'small', position: 'center' }"
            @click="addRegisterToLastCategory" />
        </div>
        <div class="w-3/12">&nbsp;</div>
      </div>
    </div>

    <!-- Footer Buttons -->
    <ButtonGroup class="mx-8 relative z-10">
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import draggable from 'vuedraggable';
import { useToastStore } from '@/components/toast/stores/toast';
import IconSettings from '@/components/icons/Settings.vue';
import Action from '@/components/buttons/Action.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';

const toast = useToastStore();
const isSaving = ref(false);

const numerals_category = ref('decimal'); // decimal, roman, alpha
const numerals_register = ref('decimal');
const id_type = ref('auto'); // auto, manual

const form = ref({
  id: '',
  number: '1.',
  title: '',
  categories: [
    {
      id: '',
      _localId: '',
      title: '',
      registers: []
    }
  ]
});

function addCategory() {
  const newCategory = {
    title: '',
    id: '',
    _localId: '',
    registers: []
  };
  form.value.categories.push(newCategory);
}

function onCategoryTitleBlur(category) {
  if (id_type.value !== 'auto') return;
  const autoId = category.title.slice(0, 2).toUpperCase();
  if (category.id !== autoId) {
    category.id = autoId;
    category._localId = autoId;
  }
}

function onCategoryIdBlur(category) {
  if (id_type.value === 'manual') {
    category.id = category._localId;
  }
}

function onRegisterTitleBlur(register) {
  if (id_type.value !== 'auto') return;
  const autoId = register.title.slice(0, 2).toUpperCase();
  if (register.id !== autoId) {
    register.id = autoId;
    register._localId = autoId;
  }
}

function onRegisterIdBlur(register) {
  if (id_type.value === 'manual') {
    register.id = register._localId;
  }
}

function addRegisterToLastCategory() {
  if (form.value.categories.length === 0) return;
  const lastCategoryIndex = form.value.categories.length - 1;
  const category = form.value.categories[lastCategoryIndex];
  category.registers.push({
    title: '',
    id: '',
    _localId: ''
  });
}

function generateId(prefix) {
  return `${prefix}-${Math.random().toString(36).substring(2, 8).toUpperCase()}`;
}

function cloneRegister(original) {
  return { ...original, id: generateId('REG') };
}

function formatNumber(index, type = 'decimal') {
  switch (type) {
    case 'alpha':
      return String.fromCharCode(65 + index).toLowerCase() + '.';
    case 'roman':
      return toRoman(index + 1) + '.';
    default:
      return `${index + 1}.`;
  }
}

function toRoman(num) {
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

<template>
  <div>
    <!-- Header -->
    <header class="mb-32 grid grid-cols-12 gap-0">
      <div class="col-span-5">
        <h2 class="font-muoto-medium">Struktur</h2>
      </div>

      <div class="col-span-7">
        <h2>Beispiel</h2>
      </div>
    </header>

    <!-- Content -->
    <InputLabel label="Kategorie" />
    <div class="grid grid-cols-12 gap-0">
      <div class="col-span-5 flex flex-col gap-y-20 border-r border-r-graphite pr-8">
        <InputGroup>
          <InputSelectButtons
            v-model="numerals_category_type"
            legend="Kategorie"
            name="numerals-category"
            label-classes="font-muoto-medium"
            wrapper-classes="flex flex-col gap-y-8"
            :options="numerals_category" />
        </InputGroup>

        <InputGroup>
          <InputLabel label="Register" />
          <InputSelectButtons
            v-model="numerals_register_type"
            legend="Register"
            name="numerals-register"
            wrapper-classes="flex flex-col gap-y-8"
            :options="numerals_register" />
        </InputGroup>

        <InputGroup>
          <InputLabel label="ID" />
          <InputSelectButtons
            v-model="id_type"
            legend="ID"
            name="id-type"
            wrapper-classes="flex flex-col gap-y-8"
            :options="ids" />
        </InputGroup>

        <div class="px-8 text-sm">
          <p>
            Standardmässig werden Kategorien und Register numerisch geordnet und die ID besteht aus den ersten beiden
            Buchstaben des Registertitels. Die Ordnungsstruktur kann jederzeit wieder angepasst werden.
          </p>
        </div>

        <!-- Buttons -->
        <ButtonGroup class="mt-108">
          <AppButton
            type="button"
            label="Speichern"
            @click="handleSave" />
          <AppButton
            type="button"
            label="Abbrechen"
            @click="onCancel" />
        </ButtonGroup>
      </div>

      <StructurePreview
        class="col-span-7"
        :category-type="numerals_category_type"
        :register-type="numerals_register_type"
        :id-type="id_type" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import StructurePreview from '@/views/archive/partials/StructurePreview.vue';
import AppButton from '@/components/buttons/Button.vue';

const props = defineProps({
  onConfirm: {
    type: Function,
    default: () => {},
  },
  onCancel: {
    type: Function,
    default: () => {},
  },
  // The current settings; the dialog edits copies and hands them to onConfirm
  categoryType: {
    type: String,
    default: 'decimal',
  },
  registerType: {
    type: String,
    default: 'decimal',
  },
  idType: {
    type: String,
    default: 'auto',
  },
});

const handleSave = () => {
  props.onConfirm({
    category: numerals_category_type.value,
    register: numerals_register_type.value,
    idType: id_type.value,
  });
};

const numerals_category = [
  { label: '1., 2., 3., 4., ...', value: 'decimal' },
  { label: 'A., B., C., D., ...', value: 'alpha' },
  { label: 'I., II., III., IV., ...', value: 'roman' },
];

const numerals_register = [
  { label: '[...]1, [...]2, [...]3, [...]4, ...', value: 'decimal' },
  { label: '[...]a, [...]b, [...]c, [...]d, ...', value: 'alpha' },
  { label: '[...]I, [...]II, [...]III, [...]IV, ...', value: 'roman' },
];

const numerals_category_type = ref(props.categoryType);
const numerals_register_type = ref(props.registerType);
const id_type = ref(props.idType);

const ids = [
  { label: 'Anfangsbuchstaben (2)', value: 'auto' },
  { label: 'Leeres Textfeld', value: 'manual' },
];
</script>

<template>
  <div>

    <!-- Header -->
    <header class="grid grid-cols-12 gap-0 mb-32">
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
            legend="Kategorie"
            v-model="numerals_category_type"
            name="numerals-category"
            labelClasses="font-muoto-medium"
            wrapperClasses="flex flex-col gap-y-8"
            :options="numerals_category" />
        </InputGroup>
        
        <InputGroup>
          <InputLabel label="Register" />
          <InputSelectButtons
            legend="Register"
            v-model="numerals_register_type"
            name="numerals-register"
            wrapperClasses="flex flex-col gap-y-8"
            :options="numerals_register" />
        </InputGroup>
        
        <InputGroup>
          <InputLabel label="ID" />
          <InputSelectButtons
            legend="ID"
            v-model="id_type"
            name="id-type"
            wrapperClasses="flex flex-col gap-y-8"
            :options="ids" />
        </InputGroup>

        <div class="text-sm px-8">
          <p>Standardmässig werden Kategorien und Register numerisch geordnet und die ID besteht aus den ersten beiden Buchstaben des Registertitels. Die Ordnungsstruktur kann jederzeit
wieder angepasst werden.</p>
        </div>

        <!-- Buttons -->
        <ButtonGroup class="mt-108">
          <AppButton type="button" label="Speichern" @click="handleSave" />
          <AppButton type="button" label="Abbrechen" @click="onCancel" />
        </ButtonGroup>
      </div>

      <StructurePreview
        class="col-span-7"
        :categoryType="numerals_category_type"
        :registerType="numerals_register_type"
        :idType="id_type" />
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
    default: () => {}
  },
  onCancel: {
    type: Function,
    default: () => {}
  },
  numerals_category_type: {
    type: String,
    default: 'decimal'
  },
  numerals_register_type: {
    type: String,
    default: 'decimal'
  },
  id_type: {
    type: String,
    default: 'auto'
  }
});

const handleSave = () => {
  props.onConfirm({
    category: numerals_category_type.value,
    register: numerals_register_type.value,
    idType: id_type.value
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

const numerals_category_type = ref(props.numerals_category_type);
const numerals_register_type = ref(props.numerals_register_type);
const id_type = ref(props.id_type);

const ids = [
  { label: 'Anfangsbuchstaben (2)', value: 'auto' },
  { label: 'Leeres Textfeld', value: 'manual' },
];

</script>

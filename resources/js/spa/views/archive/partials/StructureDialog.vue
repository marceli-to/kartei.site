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
            v-model="numerals_category_type"
            name="sections"
            labelClasses="font-muoto-medium"
            wrapperClasses="flex flex-col gap-y-8"
            :options="numerals_category" />
        </InputGroup>
        
        <InputGroup>
          <InputLabel label="Register" />
          <InputSelectButtons
            v-model="numerals_register_type"
            name="sections"
            wrapperClasses="flex flex-col gap-y-8"
            :options="numerals_register" />
        </InputGroup>
        
        <InputGroup>
          <InputLabel label="ID" />
          <InputSelectButtons
            v-model="id_type"
            name="sections"
            wrapperClasses="flex flex-col gap-y-8"
            :options="ids" />
        </InputGroup>

        <div class="text-sm px-8">
          <p>Standardmässig werden Kategorien und Register numerisch geordnet und die ID besteht aus den ersten beiden Buchstaben des Registertitels. Die Ordnungsstruktur kann jederzeit
wieder angepasst werden.</p>
        </div>

        <!-- Buttons -->
        <ButtonGroup class="mt-108">
          <ButtonPrimary type="button" label="Speichern" @click="handleSave" />
          <ButtonPrimary type="button" label="Abbrechen" @click="onCancel" />
        </ButtonGroup>
      </div>

      <div class="col-span-7 pl-8 relative before:absolute before:inset-y-0 before:left-[calc(25%_+_14px)] before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-[calc(75%_-_6px)] after:w-1 after:bg-graphite after:-z-1">
        
        <div class="flex mb-32">
          <div class="w-3/12">
            <div class="min-h-default border-y border-y-graphite w-full flex items-center justify-start">
              <IconSettings />
            </div>
          </div>
          <div class="w-6/12 px-16">
            <div class="min-h-default flex justify-between w-full text-md text-black dark:text-white pointer-events-none transition-all border-y border-graphite pr-8 !font-muoto-medium">
              <span class="w-full flex items-center justify-center">
                <span class="flex-1 text-left">Kategorie</span>
                <span class="flex-1 flex justify-start">
                  <IconPlus variant="small-bold" />
                </span>
              </span>
            </div>
          </div>
          <div class="w-3/12">
            <div class="min-h-default border-y border-y-graphite w-full flex items-center justify-start">
              <IconSettings />
            </div>
          </div>
        </div>

        <div class="flex">
          <div class="w-3/12">
            <InputLabel label="Nr." />
          </div>
          <div class="w-6/12 px-16">
            <InputLabel label="Titel" />
          </div>
          <div class="w-3/12">
            <InputLabel label="ID" />
          </div>
        </div>

        <div class="flex mb-32">
          <div class="w-3/12">
            <InputStatic class="font-muoto-medium">
              {{ formatNumber(0, numerals_category_type, true) }}
            </InputStatic>
          </div>
          <div class="w-6/12 px-16">
            <InputStatic class="font-muoto-medium">
              Beispiel
            </InputStatic>
          </div>
          <div class="w-3/12">
            <InputStatic v-html="id_type === 'auto' ? 'BE' : '&nbsp;'"/>
          </div>
        </div>
        <div class="flex mb-8" v-for="(register, index) in exampleRegisters" :key="index">
          <div class="w-3/12">
            <InputStatic>
              {{ formatNumber(0, numerals_category_type, true) }}{{ formatNumber(index, numerals_register_type) }}
            </InputStatic>
          </div>
          <div class="w-6/12 px-16">
            <InputStatic>
              {{ register.title }}
            </InputStatic>
          </div>
          <div class="w-3/12">
            <InputStatic v-html="id_type === 'auto' ? register.id : '&nbsp;'"/>
          </div>
        </div>

        <div class="flex mt-32">
          <div class="w-3/12">&nbsp;</div>
          <div class="w-6/12 px-16">
            <div class="min-h-default flex justify-between w-full font-muoto-regular text-md text-black dark:text-white pointer-events-none transition-all border-y border-graphite pr-8">
              <span class="w-full flex items-center justify-center">
                <span class="flex-1 text-left">Register</span>
                <span class="flex-1 flex justify-start">
                  <IconPlus variant="small" />
                </span>
              </span>
            </div>
          </div>
          <div class="w-3/12">&nbsp;</div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputStatic from '@/components/forms/Static.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import IconSettings from '@/components/icons/Settings.vue';
import IconPlus from '@/components/icons/Plus.vue';

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

const exampleRegisters = ref([
  { title: 'Platzhalter', id: 'PL' },
  { title: 'Blindtext', id: 'BL' }
]);

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
</script>

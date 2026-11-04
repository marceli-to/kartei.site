<template>
  <!-- Example of the chosen numbering and ID type, beside the structure settings -->
<div class="pl-8 relative before:absolute before:inset-y-0 before:left-[calc(25%_+_14px)] before:w-1 before:bg-graphite before:-z-1 after:absolute after:inset-y-0 after:left-[calc(75%_-_6px)] after:w-1 after:bg-graphite after:-z-1">
  
  <div class="flex mb-32">
    <div class="w-3/12">
      <div class="min-h-default border-y border-y-graphite w-full flex items-center justify-start">
        <Icon name="settings" />
      </div>
    </div>
    <div class="w-6/12 px-16">
      <div class="min-h-default flex justify-between w-full text-md text-black dark:text-white pointer-events-none transition-all border-y border-graphite pr-8 !font-muoto-medium">
        <span class="w-full flex items-center justify-center">
          <span class="flex-1 text-left">Kategorie</span>
          <span class="flex-1 flex justify-start">
            <Icon name="plus" variant="small-bold" />
          </span>
        </span>
      </div>
    </div>
    <div class="w-3/12">
      <div class="min-h-default border-y border-y-graphite w-full flex items-center justify-start">
        <Icon name="settings" />
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
        {{ formatNumber(0, categoryType, true) }}
      </InputStatic>
    </div>
    <div class="w-6/12 px-16">
      <InputStatic class="font-muoto-medium">
        Beispiel
      </InputStatic>
    </div>
    <div class="w-3/12">
      <InputStatic>{{ idType === 'auto' ? 'BE' : '\u00a0' }}</InputStatic>
    </div>
  </div>
  <div class="flex mb-8" v-for="(register, index) in exampleRegisters" :key="index">
    <div class="w-3/12">
      <InputStatic>
        {{ formatNumber(0, categoryType, true) }}{{ formatNumber(index, registerType) }}
      </InputStatic>
    </div>
    <div class="w-6/12 px-16">
      <InputStatic>
        {{ register.title }}
      </InputStatic>
    </div>
    <div class="w-3/12">
      <InputStatic>{{ idType === 'auto' ? register.id : '\u00a0' }}</InputStatic>
    </div>
  </div>

  <div class="flex mt-32">
    <div class="w-3/12">&nbsp;</div>
    <div class="w-6/12 px-16">
      <div class="min-h-default flex justify-between w-full font-muoto-regular text-md text-black dark:text-white pointer-events-none transition-all border-y border-graphite pr-8">
        <span class="w-full flex items-center justify-center">
          <span class="flex-1 text-left">Register</span>
          <span class="flex-1 flex justify-start">
            <Icon name="plus" variant="small" />
          </span>
        </span>
      </div>
    </div>
    <div class="w-3/12">&nbsp;</div>
  </div>

</div>
</template>

<script setup>
import InputLabel from '@/components/forms/Label.vue';
import InputStatic from '@/components/forms/Static.vue';
import { formatNumber } from '@/utils/numbering';
import Icon from '@/components/icons/Icon.vue';

defineProps({
  categoryType: {
    type: String,
    default: 'decimal'
  },
  registerType: {
    type: String,
    default: 'decimal'
  },
  idType: {
    type: String,
    default: 'auto'
  }
});

const exampleRegisters = [
  { title: 'Platzhalter', id: 'PL' },
  { title: 'Blindtext', id: 'BL' }
];
</script>

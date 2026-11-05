<template>
  <!-- Example of the chosen numbering and ID type, beside the structure settings -->
  <div
    class="relative pl-8 before:absolute before:inset-y-0 before:left-[calc(25%+14px)] before:w-1 before:bg-graphite after:absolute after:inset-y-0 after:left-[calc(75%-6px)] after:w-1 after:bg-graphite">
    <div class="mb-32 flex">
      <div class="w-3/12">
        <div class="flex min-h-default w-full items-center justify-start border-y border-y-graphite">
          <Icon name="settings" />
        </div>
      </div>
      <div class="w-6/12 px-16">
        <div
          class="pointer-events-none flex min-h-default w-full justify-between border-y border-graphite pr-8 font-muoto-medium! text-md text-black transition-all dark:text-white">
          <span class="flex w-full items-center justify-center">
            <span class="flex-1 text-left">Kategorie</span>
            <span class="flex flex-1 justify-start">
              <Icon
                name="plus"
                variant="small-bold" />
            </span>
          </span>
        </div>
      </div>
      <div class="w-3/12">
        <div class="flex min-h-default w-full items-center justify-start border-y border-y-graphite">
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

    <div class="mb-32 flex">
      <div class="w-3/12">
        <InputStatic class="font-muoto-medium">
          {{ formatNumber(0, categoryType, true) }}
        </InputStatic>
      </div>
      <div class="w-6/12 px-16">
        <InputStatic class="font-muoto-medium"> Beispiel </InputStatic>
      </div>
      <div class="w-3/12">
        <InputStatic>{{ idType === 'auto' ? 'BE' : '\u00a0' }}</InputStatic>
      </div>
    </div>
    <div
      v-for="(register, index) in exampleRegisters"
      :key="index"
      class="mb-8 flex">
      <div class="w-3/12">
        <InputStatic> {{ formatNumber(0, categoryType, true) }}{{ formatNumber(index, registerType) }} </InputStatic>
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

    <div class="mt-32 flex">
      <div class="w-3/12">&nbsp;</div>
      <div class="w-6/12 px-16">
        <div
          class="pointer-events-none flex min-h-default w-full justify-between border-y border-graphite pr-8 font-muoto-regular text-md text-black transition-all dark:text-white">
          <span class="flex w-full items-center justify-center">
            <span class="flex-1 text-left">Register</span>
            <span class="flex flex-1 justify-start">
              <Icon
                name="plus"
                variant="small" />
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

const exampleRegisters = [
  { title: 'Platzhalter', id: 'PL' },
  { title: 'Blindtext', id: 'BL' },
];
</script>

<template>
  <div class="w-full h-full flex flex-col justify-between relative pb-40">

    <!-- Categories and Registers -->
    <div class="w-full">
      <div class="flex gap-x-17 mx-8 mb-32">
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

      <!-- Categories and Registers Rows -->
      <div v-for="(category, cIdx) in form.categories" :key="`cat-${cIdx}`">
        <div class="flex gap-x-17 mx-8 mb-32">
          <div class="w-3/12">
            <InputStatic>{{ `${cIdx + 1}.` }}</InputStatic>
          </div>
          <div class="w-6/12">
            <InputText v-model="category.title" :placeholder="`Titel`" />
          </div>
          <div class="w-3/12">
            <InputText v-model="category.id" />
          </div>
        </div>

        <div v-for="(register, rIdx) in category.registers" :key="`reg-${cIdx}-${rIdx}`" class="flex gap-x-17 mx-8 mb-8">
          <div class="w-3/12">
            <InputStatic>{{ `${cIdx + 1}.${rIdx + 1}.` }}</InputStatic>
          </div>
          <div class="w-6/12">
            <InputText v-model="register.title" />
          </div>
          <div class="w-3/12">
            <InputText v-model="register.id" />
          </div>
        </div>
      </div>

      <div class="flex gap-x-17 mx-8 mb-8">
        <div class="w-3/12">&nbsp;</div>
        <div class="w-6/12">
          <Action 
            label="Register"
            :icon="{ name: 'Plus', variant: 'small', position: 'center' }"
            class="mt-32"
            @click="addRegisterToLastCategory" />
        </div>
        <div class="w-3/12">&nbsp;</div>
      </div>

    </div>

    <!-- Footer Buttons -->
    <ButtonGroup class="mx-8">
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>

  </div>
</template>

<script setup>
import { ref } from 'vue';
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

const form = ref({
  id: '',
  number: '1.',
  title: '',
  categories: [
    {
      title: '',
      id: '',
      registers: []
    }
  ]
});

function addCategory() {
  const newCategory = {
    title: '',
    id: '',
    registers: []
  };
  form.value.categories.push(newCategory);
}

function addRegisterToLastCategory() {
  if (form.value.categories.length === 0) return;
  const lastCategoryIndex = form.value.categories.length - 1;
  const newRegister = {
    title: '',
    id: ''
  };
  form.value.categories[lastCategoryIndex].registers.push(newRegister);
}
</script>

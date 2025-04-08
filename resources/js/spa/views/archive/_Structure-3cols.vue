<template>
  <div class="w-full h-full flex flex-col justify-between relative">

    <!-- -->
    <div class="flex h-full items-center justify-between w-full">

      <!-- Left Panel -->
      <div class="w-3/12 h-full border-r border-graphite">
        <div class="mx-8 flex flex-col gap-y-16">
          <button 
            type="button" 
            class="min-h-default border-y border-y-graphite flex items-center justify-start">
            <IconSettings />
          </button>

          <div>
            <InputLabel label="Nr." />
            <div class="flex flex-col gap-y-8">
              <div 
                v-for="(category, cIdx) in form.categories" 
                :key="`cat-${cIdx}`">
                <InputStatic>{{ `${cIdx + 1}.` }}</InputStatic>
                <div 
                  v-for="(register, rIdx) in category.registers" 
                  :key="`reg-${cIdx}-${rIdx}`"
                  class="my-8 last:mb-0">
                  <InputStatic>{{ `${cIdx + 1}.${rIdx + 1}` }}</InputStatic>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Middle Panel -->
      <div class="w-6/12 h-full border-r border-graphite">
        <div class="mx-8 flex flex-col gap-y-16">
          <Action 
            label="Kategorie" 
            classes="!font-muoto-medium"
            :icon="{ name: 'Plus', variant: 'small-bold', position: 'center' }"
            @click="addCategory" />

          <div>
            <InputLabel label="Titel" />
            <div v-for="(category, cIdx) in form.categories" :key="`middle-${cIdx}`">
              <InputText
                v-model="category.title"
                :id="`title-${cIdx}`"
                :placeholder="`Kategorie ${cIdx + 1}`"
                aria-label="Titel"
                class="mb-8" />
              <div 
                v-for="(register, rIdx) in category.registers" 
                :key="`register-${cIdx}-${rIdx}`"
                class="my-8">
                <InputText
                  v-model="register.title"
                  :id="`register-title-${cIdx}-${rIdx}`"
                  :placeholder="''"
                  aria-label="Register Titel" />
              </div>
            </div>

            <Action 
              label="Register"
              :icon="{ name: 'Plus', variant: 'small', position: 'center' }"
              class="mt-32"
              @click="addRegisterToLastCategory" />
          </div>
        </div>
      </div>

      <!-- Right Panel -->
      <div class="w-3/12 h-full">
        <div class="mx-8 flex flex-col gap-y-16">
          <button 
            type="button" 
            class="min-h-default border-y border-y-graphite flex items-center justify-start">
            <IconSettings />
          </button>

          <div>
            <InputLabel label="ID" />
            <div class="flex flex-col gap-y-8">
              <div 
                v-for="(category, cIdx) in form.categories" 
                :key="`right-${cIdx}`">
                <InputText
                  v-model="category.id"
                  :id="`id-${cIdx}`"
                  :placeholder="''"
                  aria-label="ID" />
                <div 
                  v-for="(register, rIdx) in category.registers" 
                  :key="`reg-id-${cIdx}-${rIdx}`"
                  class="my-8 last:mb-0">
                  <InputText
                    v-model="register.id"
                    :id="`reg-id-${cIdx}-${rIdx}`"
                    :placeholder="''"
                    aria-label="Register ID" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Buttons -->
    <ButtonGroup>
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
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
const toast = useToastStore();

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

const errors = ref({
  id: null,
  number: null,
  title: null
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

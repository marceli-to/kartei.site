<template>
  <div class="w-full h-full flex flex-col justify-between relative">
    <div class="flex h-full items-center justify-between w-full">
      <div class="w-3/12 h-full border-r border-graphite">
        <div class="mx-8 flex flex-col gap-y-32">
          
          <button 
            type="button" 
            class="min-h-default border-y border-y-graphite flex items-center justify-start">
            <IconSettings />
          </button>

          <InputGroup>
            <InputLabel label="Nr." id="number" />
            <InputStatic>1.</InputStatic>
          </InputGroup>

          <div class="flex flex-col gap-y-8">
            <InputGroup>
              <InputStatic>1.1</InputStatic>
            </InputGroup>
            <InputGroup>
              <InputStatic>1.2</InputStatic>
            </InputGroup>
          </div>
        </div>
      </div>
      <div class="w-6/12 h-full border-r border-graphite">
        <div class="mx-8 flex flex-col gap-y-32">
          <Action 
            label="Kategorie" 
            classes="!font-muoto-medium"
            :icon="{ name: 'Plus', variant: 'small-bold', position: 'center' }" />

          <InputGroup>
            <InputLabel label="Titel" id="title" />
            <InputText
              v-model="form.title"
              id="title"
              :error="errors.title"
              @update:error="errors.title = $event"
              :placeholder="errors.title ? errors.title : 'Titel'"
              aria-label="Titel" />
          </InputGroup>

          <div class="flex flex-col gap-y-8">
            <InputGroup>
              <InputText
                v-model="form.title"
                id="title"
                :error="errors.title"
                @update:error="errors.title = $event"
                :placeholder="errors.title ? errors.title : 'Titel'"
                aria-label="Titel" />
            </InputGroup>
            <InputGroup>
              <InputText
                v-model="form.title"
                id="title"
                :error="errors.title"
                @update:error="errors.title = $event"
                :placeholder="errors.title ? errors.title : 'Titel'"
                aria-label="Titel" />
            </InputGroup>
          </div>

          <Action 
            label="Register" 
            :icon="{ name: 'Plus', variant: 'small', position: 'center' }" />
        </div>
      </div>
      <div class="w-3/12 h-full">
        <div class="mx-8 flex flex-col gap-y-32">
          <button 
            type="button" 
            class="min-h-default border-y border-y-graphite flex items-center justify-start">
            <IconSettings />
          </button>

          <InputGroup>
            <InputLabel label="ID" id="id" />
            <InputText
              v-model="form.id"
              id="id"
              :error="errors.id"
              @update:error="errors.id = $event"
              :placeholder="errors.id ? errors.id : ''"
              aria-label="ID" />
          </InputGroup>

          <div class="flex flex-col gap-y-8">
            <InputGroup>
              <InputText
                v-model="form.id"
                id="id"
                :error="errors.id"
                @update:error="errors.id = $event"
                :placeholder="errors.id ? errors.id : ''"
                aria-label="ID" />
            </InputGroup>
            <InputGroup>
              <InputText
                v-model="form.id"
                id="id"
                :error="errors.id"
                @update:error="errors.id = $event"
                :placeholder="errors.id ? errors.id : ''"
                aria-label="ID" />
            </InputGroup>
          </div>
        </div>
      </div>
    </div>
    <ButtonGroup class="absolute bottom-40 z-20 w-[calc(100%_-_16px)] mx-8 flex flex-col">
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
    </ButtonGroup>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import IconSettings from '@/components/icons/Settings.vue';
import Action from '@/components/buttons/Action.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import { useToastStore } from '@/components/toast/stores/toast';

const toast = useToastStore();

const form = ref({
  id: '',
  number: '1.',
  title: ''
});

const errors = ref({
  id: null,
  number: null,
  title: null
});
</script>
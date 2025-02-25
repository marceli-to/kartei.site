<template>
  <div class="px-8 relative w-full flex flex-col justify-between pb-16 h-full -top-24">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputLabel label="Vorname" id="firstname" required />
        <InputText
          v-model="form.firstname"
          id="firstname"
          :error="errors.firstname"
          @update:error="errors.firstname = $event"
          :placeholder="errors.firstname ? errors.firstname : 'Vorname'"
          aria-label="Vorname" />
      </InputGroup>
      <InputGroup>
        <InputLabel label="Name" id="name" required />
        <InputText
          v-model="form.name"
          id="name"
          :error="errors.name"
          @update:error="errors.name = $event"
          :placeholder="errors.name ? errors.name : 'Nachname'"
          aria-label="Nachname" />
      </InputGroup>
      <InputGroup>
        <InputLabel label="E-Mail-Adresse" id="email" required />
        <InputText
          v-model="form.email"
          id="email"
          :error="errors.email"
          @update:error="errors.email = $event"
          :placeholder="errors.email ? errors.name : 'E-Mail-Adresse'"
          aria-label="E-Mail-Adresse" />
      </InputGroup>
    </div>
    <div>
      <ButtonGroup>
        <ButtonPrimary label="Abbrechen" type="button" />
        <ButtonPrimary label="Speichern" />
      </ButtonGroup>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import InputGroup from '@/components/fields/Group.vue';
import InputLabel from '@/components/fields/Label.vue';
import InputText from '@/components/fields/Text.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';

const form = ref({
  firstname: '',
  name: '',
});

const errors = ref({
  firstname: null,
  name: null,
});
</script>
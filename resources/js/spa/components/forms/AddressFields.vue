<template>
  <!-- Address fields shared by the address, billing address and company forms:
       <AddressFields v-model:form="form" v-model:errors="errors" />
       The fields are edited in place; an error clears on the field's first change. -->
  <div class="flex flex-col gap-y-20">
    <InputGroup v-for="field in fields" :key="field.key">
      <InputLabel
        :label="field.label"
        :id="`${idPrefix}${field.key}`"
        :required="field.required"
        :error="errors[field.key]" />
      <InputSelect
        v-if="field.key === 'country'"
        :id="`${idPrefix}${field.key}`"
        v-model="form[field.key]"
        :options="countries"
        :error="errors[field.key]"
        @update:error="errors[field.key] = $event" />
      <InputText
        v-else
        :id="`${idPrefix}${field.key}`"
        v-model="form[field.key]"
        :placeholder="field.label"
        :error="errors[field.key]"
        @update:error="errors[field.key] = $event" />
    </InputGroup>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { countries } from '@/config/countries';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputSelect from '@/components/forms/Select.vue';

const form = defineModel('form', { type: Object, required: true });
const errors = defineModel('errors', { type: Object, required: true });

const props = defineProps({
  // Keeps ids unique when two address forms are on the same page
  idPrefix: {
    type: String,
    default: ''
  },
  // Companies: "name" (required), no Adresszusatz; addresses: "company" + "byline"
  nameField: {
    type: String,
    default: 'company',
    validator: (value) => ['company', 'name'].includes(value)
  },
});

const fields = computed(() => [
  { key: props.nameField, label: 'Bezeichnung / Firma', required: props.nameField === 'name' },
  ...(props.nameField === 'company' ? [{ key: 'byline', label: 'Adresszusatz' }] : []),
  { key: 'street', label: 'Strasse', required: true },
  { key: 'street_number', label: 'Hausnummer' },
  { key: 'zip', label: 'PLZ', required: true },
  { key: 'city', label: 'Ort', required: true },
  { key: 'country', label: 'Land', required: true },
]);
</script>

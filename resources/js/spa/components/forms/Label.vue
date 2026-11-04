<template>
  <!-- With an error, the message takes the label's place: always visible, even when
       the field has a value (a placeholder only shows while it is empty) -->
  <label
    :for="id"
    :class="[classes, { 'text-flame': error }]">
    <template v-if="error">{{ error }}</template>
    <template v-else>{{ label }} <span v-if="required">*</span></template>
  </label>
</template>
<script setup>
defineProps({
  id: {
    type: String,
    default: null,
  },
  label: {
    type: String,
    required: true,
  },
  required: {
    type: Boolean,
    default: false,
  },
  // Server message for the field, shown instead of the label
  error: {
    type: String,
    default: null,
  },
  classes: {
    type: String,
    default: 'text-sm mb-4 block',
  },
});
</script>

<template>
  <label :for="id" :class="classes">
    {{ label }} <span v-if="required">*</span>
  </label>
</template>
<script setup>
const props = defineProps({
  id: {
    type: String,
    default: null
  },
  label: {
    type: String,
    required: true
  },
  required: {
    type: Boolean,
    default: false
  },
  classes: {
    type: String,
    default: 'text-sm mb-4 block'
  }
});
</script>
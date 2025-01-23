<template>
  <h1>Playground</h1>
  <p>Count: {{ count }}</p>
  <button @click="increment">Increment</button>
  <FileUpload v-bind="config" />
</template>

<script setup>
import { useMainStore } from '../store';
import { storeToRefs } from 'pinia';
import ImageUpload from '../components/ImageUpload.vue';
import FileUpload from '../components/FileUpload.vue'

const store = useMainStore();
const { count } = storeToRefs(store);

const config = {
  maxSize: 10 * 1024 * 1024, // 10MB
  allowedTypes: ['image/*'],
  uploadUrl: '/api/upload',
  multiple: true
}
const increment = store.increment;
</script>
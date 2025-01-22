<template>
  <h1>Playground</h1>
  <p>Count: {{ count }}</p>
  <button @click="increment">Increment</button>
  <ImageUpload />
</template>

<script setup>
import { useMainStore } from '../store';
import { storeToRefs } from 'pinia';
import ImageUpload from '../components/ImageUpload.vue';

const store = useMainStore();
const { count } = storeToRefs(store);

const increment = store.increment;
</script>
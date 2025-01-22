<template>
  <h1>Playground</h1>
  <p>Count: {{ count }}</p>
  <button @click="increment">Increment</button>
</template>
<script>
import { useMainStore } from '../store';
import { storeToRefs } from 'pinia';

export default {
  setup() {
    const store = useMainStore();
    const { count } = storeToRefs(store);

    return {
      count,
      increment: store.increment,
    };
  },
};
</script>
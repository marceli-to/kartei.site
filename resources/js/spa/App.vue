<template>
  <div id="app">
    <NavComponent />
    <router-view></router-view>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import NavComponent from './components/Nav.vue';

export default defineComponent({
  name: 'App',
  components: {
    NavComponent,
  },
  setup() {
  }
});
</script>
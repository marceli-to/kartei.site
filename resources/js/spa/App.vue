<template>
  <div id="app">
    <NavComponent />
    <router-view></router-view>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { usePermissionStore } from './store/permissions';
import NavComponent from './components/Nav.vue';

const permissionStore = usePermissionStore();

onMounted(() => {
  permissionStore.initialize();
});
</script>
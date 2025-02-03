<template>
  <NavComponent />
  <router-view></router-view>
</template>

<script setup>
import NavComponent from './components/Nav.vue';
import { onMounted } from 'vue'
import { usePermissionStore } from './store/permissions'

const permissionStore = usePermissionStore()

onMounted(() => {
  permissionStore.initialize()
})
</script>
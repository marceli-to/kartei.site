<template>
  <h1>Users</h1>
  <template v-if="userStore.can('view.records')">
    <p>Can view records</p>
  </template>
  <template v-if="userStore.can('delete.records')">
    <p>Can delete records</p>
  </template>
  <template v-if="userStore.can('edit.records')">
    <p>Can edit records</p>
  </template>
</template>
<script setup>
import { useUserStore } from '@/stores/user'
const userStore = useUserStore();
</script>
<template>
  <h1>Users</h1>
  <template v-if="permissionStore.can('view.records')">
    <p>Can view records</p>
  </template>
  <template v-if="permissionStore.can('delete.records')">
    <p>Can delete records</p>
  </template>
  <template v-if="permissionStore.can('edit.records')">
    <p>Can edit records</p>
  </template>
</template>
<script setup>
import { usePermissionStore } from '@/store/permissions'
const permissionStore = usePermissionStore();
</script>
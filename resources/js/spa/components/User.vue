<template>
  <div class="fixed right-10 bottom-10 bg-gray-200 px-10 py-5 text-xs leading-snug">
    {{ userStore.user.firstname }} {{ userStore.user.name }}, 
    {{ userStore.user.email }},
    {{ userStore.roles.map(role => role.name).join(', ') }}
  </div>
</template>
<script setup>
import { useUserStore } from '@/store/user'
const userStore = useUserStore();
</script>
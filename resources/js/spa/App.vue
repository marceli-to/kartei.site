<template>
  <NavComponent />
  <UserComponent />
  <router-view></router-view>
</template>

<script setup>
import NavComponent from './components/Nav.vue';
import UserComponent from './components/User.vue';
import { onMounted } from 'vue';
import { useUserStore } from '@/store/user';

const userStore = useUserStore()

onMounted(() => {
  userStore.initialize()
})
</script>
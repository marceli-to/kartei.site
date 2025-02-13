<template>
  <NavComponent />
  <UserComponent />
  <router-view></router-view>
  <ToastComponent />
</template>

<script setup>
import NavComponent from '@/components/Nav.vue';
import UserComponent from '@/components/User.vue';
import ToastComponent from './components/Toast.vue'; // Import the Toast component
import { onMounted } from 'vue';
import { useUserStore } from '@/store/user';
const userStore = useUserStore()

onMounted(() => {
  userStore.initialize()
})
</script>
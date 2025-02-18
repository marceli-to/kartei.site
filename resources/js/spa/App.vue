<template>
  <NavComponent />
  <UserComponent />
  <router-view></router-view>
  <ToastComponent />
  <DialogComponent />
</template>

<script setup>
import NavComponent from '@/components/Nav.vue';
import UserComponent from '@/components/User.vue';
import ToastComponent from '@/components/Toast.vue';
import DialogComponent from '@/components/Dialog.vue';
import { onMounted } from 'vue';
import { useUserStore } from '@/store/user';
const userStore = useUserStore()

onMounted(() => {
  userStore.initialize()
})
</script>
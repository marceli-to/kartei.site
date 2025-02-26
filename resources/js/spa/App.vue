<template>
  <Header />
  <router-view></router-view>
  <Toast />
  <Dialog />
  <User />
</template>

<script setup>
import User from '@/components/User.vue';
import Toast from '@/components/Toast.vue';
import Dialog from '@/components/Dialog.vue';
import Header from '@/components/Header.vue';
import { onMounted } from 'vue';
import { useUserStore } from '@/stores/user';
const userStore = useUserStore()

onMounted(() => {
  userStore.initialize()
})
</script>
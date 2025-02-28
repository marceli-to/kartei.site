<template>
  <Header />
  <router-view></router-view>
  <Toast />
  <Dialog />
  <User />
</template>

<script setup>
import User from '@/components/User.vue';
import Toast from '@/components/toast/Toast.vue';
import Dialog from '@/components/dialog/Dialog.vue';
import Header from '@/components/layout/Header.vue';
import { onMounted } from 'vue';
import { useUserStore } from '@/stores/user';
import { useTheme } from '@/composables/useTheme';

const { fetchTheme } = useTheme();
const userStore = useUserStore()

onMounted(() => {
  userStore.initialize();
  fetchTheme();
})
</script>
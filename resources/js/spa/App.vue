<template>
  <Header :title="pageTitle" />
  <router-view v-slot="{ Component }">
    <component :is="Component" ref="pageComponent" />
  </router-view>
  <Toast />
  <Dialog />
  <User />
</template>

<script setup>
import { ref, watchEffect, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useUserStore } from '@/stores/user';
import { useTheme } from '@/composables/useTheme';
import User from '@/components/User.vue';
import Toast from '@/components/toast/Toast.vue';
import Dialog from '@/components/dialog/Dialog.vue';
import Header from '@/components/layout/Header.vue';


const { fetchTheme } = useTheme();
const userStore = useUserStore()

const route = useRoute();
const pageComponent = ref(null);
const pageTitle = ref('');

onMounted(() => {
  userStore.initialize();
  fetchTheme();
});

watchEffect(() => {
  if (pageComponent.value?.title) {
    pageTitle.value = pageComponent.value.title;
  }
});
</script>
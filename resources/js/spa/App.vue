<template>
  <template v-if="!isLoading">
    <Header :title="pageTitle" />
    <router-view v-slot="{ Component }">
      <component :is="Component" :key="route.fullPath" ref="pageComponent" />
    </router-view>
    <Toast />
    <Dialog />
    <User />
  </template>
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
const isLoading = ref(true);

onMounted(async () => {
  await userStore.initialize();
  await fetchTheme();
  isLoading.value = false;
});

watchEffect(() => {
  if (pageComponent.value?.title) {
    pageTitle.value = pageComponent.value.title;
  }
});
</script>
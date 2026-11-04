<template>
  <Header />

  <!-- Keyed by path: query changes (e.g. ?section=) must not remount the page -->
  <router-view v-slot="{ Component }">
    <component
      :is="Component"
      :key="route.path" />
  </router-view>

  <Toast />
  <Dialog />

  <!-- Global overlay (always mounted, shows when activeCount > 0) -->
  <LoadingOverlay />
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useUserStore } from '@/stores/user';
import { useTheme } from '@/composables/useTheme';
import { useGlobalLoading } from '@/composables/useGlobalLoading';

import Toast from '@/components/toast/Toast.vue';
import Dialog from '@/components/dialog/Dialog.vue';
import Header from '@/components/layout/Header.vue';
import LoadingOverlay from '@/components/LoadingOverlay.vue';

const { fetchTheme } = useTheme();
const userStore = useUserStore();
const route = useRoute();
const { show, hide } = useGlobalLoading();

onMounted(async () => {
  // App boot loading
  show();
  try {
    await userStore.initialize();
    await fetchTheme();
  } finally {
    hide();
  }
});
</script>

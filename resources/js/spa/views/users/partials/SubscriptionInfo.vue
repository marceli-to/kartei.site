<template>
  <div class="bg-ice text-sm p-8 mt-32">
    <slot />
    <router-link 
      :to="{ name: 'settings' }"
      class="font-muoto-medium flex items-center gap-x-4 mt-4">
      <IconChevronRight variant="tiny" />
      Abonnement erstellen
    </router-link>
  </div>
</template>
<script setup>
import IconChevronRight from '@/components/icons/ChevronRight.vue';
</script>

<template>
  <div class="bg-[var(--theme-color)] text-sm p-8 mt-32">
    <div>
      <p v-if="hasSubscription && maxUsersReached">
        Die maximale Anzahl von Benutzern für dein Abonnement ist erreicht. Um weitere Benutzer hinzuzufügen, musst du dein Abonnement upgraden.
      </p>
      <p v-else-if="!hasSubscription">
        Du hast noch kein Abonnement. Um Benutzer hinzuzufügen, musst du ein Abonnement erstellen.
      </p>
      <router-link 
        :to="{ name: 'settings' }"
        class="font-muoto-medium flex items-center gap-x-4 mt-4">
        <IconChevronRight variant="tiny" />
        Abonnement erstellen
      </router-link>
    </div>
  </div>
</template>
<script setup>
import IconChevronRight from '@/components/icons/ChevronRight.vue';

const props = defineProps({
  hasSubscription: {
    type: Boolean,
    required: true
  },
  maxUsersReached: {
    type: Boolean,
    required: true
  }
});
</script>
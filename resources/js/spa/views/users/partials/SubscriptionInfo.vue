<template>
  <div class="mt-32 bg-[var(--theme-color)] p-8 text-sm">
    <div>
      <p v-if="hasSubscription && maxUsersReached">
        Die maximale Anzahl von Benutzern für Ihr Abonnement ist erreicht. Um weitere Benutzer hinzuzufügen, müssen Sie
        Ihr Abonnement upgraden.
      </p>
      <p v-else-if="!hasSubscription">
        Sie haben noch kein Abonnement. Um Benutzer hinzuzufügen, müssen Sie ein Abonnement erstellen.
      </p>
      <router-link
        :to="{ name: 'settings', query: { section: 'subscription' } }"
        class="mt-4 flex items-center gap-x-4 font-muoto-medium">
        <Icon
          name="chevron-right"
          variant="tiny" />
        Abonnement erstellen
      </router-link>
    </div>
  </div>
</template>
<script setup>
import Icon from '@/components/icons/Icon.vue';

defineProps({
  hasSubscription: {
    type: Boolean,
    required: true,
  },
  maxUsersReached: {
    type: Boolean,
    required: true,
  },
});
</script>

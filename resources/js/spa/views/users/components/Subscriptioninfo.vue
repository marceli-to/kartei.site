<template>
  <div 
    class="bg-[var(--theme-color)] text-sm p-8 mt-32"
    v-if="showSubscriptionInfo">
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
import { watch, onMounted, computed } from 'vue';
import { useSubscriptionInfo } from '@/views/users/components/useSubscriptionInfo';
import IconChevronRight from '@/components/icons/ChevronRight.vue';

const emit = defineEmits(['update:disableListUsers']);

const props = defineProps({
  users: {
    type: Array,
    required: true
  },
  isActive: {
    type: Boolean,
    default: false
  }
});

const isActiveRef = computed(() => props.isActive);

const {
  hasSubscription,
  maxUsersReached,
  showSubscriptionInfo,
  disableListUsers,
  fetchSubscription
} = useSubscriptionInfo(props.users, isActiveRef);

// Emit to parent whenever value changes
watch(disableListUsers, (val) => {
  emit('update:disableListUsers', val);
});

watch(isActiveRef, async (active) => {
  if (active) await fetchSubscription();
});

onMounted(async () => {
  if (props.isActive) await fetchSubscription();
});
</script>
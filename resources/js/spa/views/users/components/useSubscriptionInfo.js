import { ref, computed } from 'vue';
import { getUserSubscription } from '@/services/api/user';

export function useSubscriptionInfo(users, isActive) {
  const subscription = ref(null);

  const hasSubscription = computed(() => subscription.value !== null);

  const maxUsersReached = computed(() => {
    if (!subscription.value?.max_users) return false;
    return users.value?.length >= subscription.value.max_users;
  });

  const showSubscriptionInfo = computed(() => {
    return isActive.value && (!hasSubscription.value || maxUsersReached.value);
  });

  const disableListUsers = computed(() => {
    return !hasSubscription.value || maxUsersReached.value;
  });

  const fetchSubscription = async () => {
    try {
      const response = await getUserSubscription();
      subscription.value = response.data || null;
    } 
    catch (error) {
      console.error('Failed to fetch user subscription:', error);
    }
  };

  return {
    subscription,
    hasSubscription,
    maxUsersReached,
    showSubscriptionInfo,
    disableListUsers,
    fetchSubscription
  };
}

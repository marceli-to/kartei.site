import api from '@/services/api/axios'

export const getSubscriptionPlans = async () => {
  const response = await api.get('/subscription-plans');
  return response.data;
};

export const getUserSubscription = async () => {
  const response = await api.get('/user/subscription');
  return response.data;
}

export const updateUserSubscription = async (subscriptionData) => {
  const response = await api.put('/user/subscription', subscriptionData);
  return response.data;
}

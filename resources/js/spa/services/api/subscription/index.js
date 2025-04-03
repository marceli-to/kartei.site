import api from '@/services/api/axios'

export const getSubscriptionPlans = async () => {
  const response = await api.get('/subscription-plans');
  return response.data;
};

import axios from 'axios';

export const getSubscriptionPlans = async () => {
  const response = await axios.get('/api/subscription-plans');
  return response.data;
};

import axios from 'axios';

export const getUser = async () => {
  const response = await axios.get('/api/user');
  return response.data;
};

export const updateUser = async (userData) => {
  const response = await axios.put(`/api/user`, userData);
  return response.data;
};

export const updatePassword = async (passwordData) => {
  const response = await axios.post(`/api/user/password`, passwordData);
  return response.data;
};

export const getUserAddress = async () => {
  const response = await axios.get('/api/user/address');
  return response.data;
};

export const getUserBillingAddress = async () => {
  const response = await axios.get('/api/user/billing-address');
  return response.data;
}

export const updateUserAddress = async (addressData) => {
  const response = await axios.put(`/api/user/address`, addressData);
  return response.data;
};

export const updateUserBillingAddress = async (addressData) => {
  const response = await axios.put(`/api/user/billing-address`, addressData);
  return response.data;
};
import api from '@/services/api/axios';

export const getUserPermissions = async () => {
  const response = await api.get('/user/permissions');
  return response.data;
};

// Archives that are deleted together with the account

export const getOwnedArchives = async () => {
  const response = await api.get('/user/owned-archives');
  return response.data;
};

export const getUserProfile = async () => {
  const response = await api.get('/user/profile');
  return response.data;
};

export const updateUserProfile = async (userData) => {
  const response = await api.put('/user/profile', userData);
  return response.data;
};

export const updatePassword = async (passwordData) => {
  const response = await api.post('/user/password', passwordData);
  return response.data;
};

export const getUserAddress = async () => {
  const response = await api.get('/user/address');
  return response.data;
};

export const getUserBillingAddress = async () => {
  const response = await api.get('/user/billing-address');
  return response.data;
};

export const updateUserAddress = async (addressData) => {
  const response = await api.put('/user/address', addressData);
  return response.data;
};

export const updateUserBillingAddress = async (addressData) => {
  const response = await api.put('/user/billing-address', addressData);
  return response.data;
};

export const getUserTheme = async () => {
  const response = await api.get('/user/theme');
  return response.data;
};

export const updateUserTheme = async (themeData) => {
  const response = await api.put('/user/theme', themeData);
  return response.data;
};

// Deletes the logged-in user's own account (and the archives they own)
export const deleteAccount = async () => {
  const response = await api.delete('/user');
  return response.data;
};

import api from '@/services/api/axios'

export const getUserPermissions = async () => {
  const response = await api.get('/user/permissions');
  return response.data;
}

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
}

export const updateUserAddress = async (addressData) => {
  const response = await api.put('/user/address', addressData);
  return response.data;
};

export const updateUserBillingAddress = async (addressData) => {
  const response = await api.put('/user/billing-address', addressData);
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

export const deleteUser = async () => {
  const response = await api.delete('/user');
  return response.data;
}

export const getUserTheme = async () => {
  const response = await api.get('/user/theme');
  return response.data;
}

export const updateUserTheme = async (themeData) => {
  const response = await api.put('/user/theme', themeData);
  return response.data;
}

export const getUserCompanies = async () => {
  const response = await api.get('/user/companies');
  return response.data;
}

export const createUserCompany = async (companyData) => {
  const response = await api.post('/user/company', companyData);
  return response.data;
}

export const updateUserCompany = async (companyData) => {
  const response = await api.put(`/user/company/${companyData.uuid}`, companyData);
  return response.data;
}

export const deleteUserCompany = async (uuid) => {
  const response = await api.delete(`/user/company/${uuid}`);
  return response.data;
}

export const storePermissions = async (user, formData) => {
  const response = await api.put(`/user/permissions/store/${user.uuid}`, formData);
  return response.data;
};

export const updatePermissions = async (user, formData) => {
  const response = await api.put(`/user/permissions/update/${user.uuid}`, formData);
  return response.data;
};  

export const sendInvitation = async (user, archive) => {
  const response = await api.post(`/user/invite/${user.uuid}`, archive);
  return response.data;
};

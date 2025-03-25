import axios from 'axios';

export const createUser = async (userData) => {
  const response = await axios.post('/api/user', userData);
  return response.data;
};

export const getUserPermissions = async () => {
  const response = await axios.get('/api/user/permissions');
  return response.data;
}

export const getUserProfile = async () => {
  const response = await axios.get('/api/user/profile');
  return response.data;
};

export const updateUserProfile = async (userData) => {
  const response = await axios.put('/api/user/profile', userData);
  return response.data;
};

export const updatePassword = async (passwordData) => {
  const response = await axios.post('api/user/password', passwordData);
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
  const response = await axios.put('/api/user/address', addressData);
  return response.data;
};

export const updateUserBillingAddress = async (addressData) => {
  const response = await axios.put('/api/user/billing-address', addressData);
  return response.data;
};

export const getUserSubscription = async () => {
  const response = await axios.get('/api/user/subscription');
  return response.data;
}

export const updateUserSubscription = async (subscriptionData) => {
  const response = await axios.put('/api/user/subscription', subscriptionData);
  return response.data;
}

export const deleteUser = async () => {
  const response = await axios.delete('/api/user');
  return response.data;
}

export const getUserTheme = async () => {
  const response = await axios.get('/api/user/theme');
  return response.data;
}

export const updateUserTheme = async (themeData) => {
  const response = await axios.put('/api/user/theme', themeData);
  return response.data;
}

export const getUserCompanies = async () => {
  const response = await axios.get('/api/user/companies');
  return response.data;
}

export const createUserCompany = async (companyData) => {
  const response = await axios.post('/api/user/company', companyData);
  return response.data;
}

export const updateUserCompany = async (companyData) => {
  const response = await axios.put(`/api/user/company/${companyData.uuid}`, companyData);
  return response.data;
}

export const deleteUserCompany = async (uuid) => {
  const response = await axios.delete(`/api/user/company/${uuid}`);
  return response.data;
}

export const getRelatedUsers = async () => {
  const response = await axios.get('/api/user/related');
  return response.data;
}

export const storePermissions = async (user, formData) => {
  const response = await axios.put(`/api/user/permissions/${user.uuid}`, formData);
  return response.data;
};  

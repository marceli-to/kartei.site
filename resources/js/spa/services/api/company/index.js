import api from '@/services/api/axios'

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

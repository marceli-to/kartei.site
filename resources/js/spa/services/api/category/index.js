import api from '@/services/api/axios'

export const getCategories = async (uuid) => {
  const response = await api.get(`/categories/${uuid}`);
  return response.data;
};

export const storeCategory = async (uuid, category) => {
  const response = await api.put(`/category/${uuid}`, category);
  return response.data;
};

export const getCategoriesAndRegisters = async (uuid) => {
  const response = await api.get(`/categories/registers/${uuid}`);
  return response.data;
};

export const deleteCategory = async (categoryUuid) => {
  const response = await api.delete(`/category/${categoryUuid}`);
  return response.data;
};

export const deleteRegister = async (registerUuid) => {
  const response = await api.delete(`/category/register/${registerUuid}`);
  return response.data;
};
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
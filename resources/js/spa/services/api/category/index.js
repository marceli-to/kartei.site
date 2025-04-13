import api from '@/services/api/axios'

export const getCategory = async (uuid) => {
  const response = await api.get(`/category/${uuid}`);
  return response.data;
};

export const storeCategory = async (uuid, category) => {
  const response = await api.put(`/category/${uuid}`, category);
  return response.data;
};

export const getCategoriesAndRegisters = async (uuid) => {
  const response = await api.get(`/category/categories/${uuid}`);
  return response.data;
};
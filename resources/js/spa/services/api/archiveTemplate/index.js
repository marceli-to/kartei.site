import api from '@/services/api/axios'

export const getTemplate = async (uuid) => {
  const response = await api.get(`/archive/template/${uuid}`);
  return response.data;
};

export const storeTemplate = async (uuid, template) => {
  const response = await api.put(`/archive/template/${uuid}`, template);
  return response.data;
};

export const deleteTemplate = async (uuid) => {
  const response = await api.delete(`/archive/template/${uuid}`);
  return response.data;
};

export const deleteTemplateField = async (uuid) => {
  const response = await api.delete(`/archive/template/field/${uuid}`);
  return response.data;
};
  
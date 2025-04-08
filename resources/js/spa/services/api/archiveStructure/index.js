import api from '@/services/api/axios'

export const getStructure = async (uuid) => {
  const response = await api.get(`/archive/structure/${uuid}`);
  return response.data;
};

export const storeStructure = async (uuid, structure) => {
  const response = await api.put(`/archive/structure/${uuid}`, structure);
  return response.data;
};

export const deleteStructur = async (uuid) => {
  const response = await api.delete(`/archive/structure/${uuid}`);
  return response.data;
};
  
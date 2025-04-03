import api from '@/services/api/axios'

export const getTags = async (uuid) => {
  const response = await api.get(`/tags/${uuid}`);
  return response.data;
};

export const storeTags = async (uuid, tags) => {
  const response = await api.put(`/tags/${uuid}`, {tags});
  return response.data;
};

export const deleteTag = async (uuid) => {
  const response = await api.delete(`/tag/${uuid}`);
  return response.data;
};
  

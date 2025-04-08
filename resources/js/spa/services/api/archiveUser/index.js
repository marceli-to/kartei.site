import api from '@/services/api/axios'

export const findUser = async (uuid) => {
  const response = await api.get(`/archive/user/${uuid}`);
  return response.data;
};

export const createUser = async (userData) => {
  const response = await api.post('/archive/user', userData);
  return response.data;
};

export const deleteUser = async (uuid) => {
  const response = await api.delete(`/archive/user/${uuid}`);
  return response.data;
};

export const updateUser = async (uuid, userData) => {
  const response = await api.put(`/archive/user/${uuid}`, userData);
  return response.data;
};

export const getRelatedUsers = async () => {
  const response = await api.get('/archive/users/related');
  return response.data;
}

export const getArchiveUsers = async (uuid) => {
  const response = await api.get(`/archive/users/${uuid}`);
  return response.data;
};


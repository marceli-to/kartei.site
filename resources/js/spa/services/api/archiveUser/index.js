import axios from 'axios';

export const findUser = async (uuid) => {
  const response = await axios.get(`/api/archive/user/${uuid}`);
  return response.data;
};

export const createUser = async (userData) => {
  const response = await axios.post('/api/archive/user', userData);
  return response.data;
};

export const deleteUser = async (uuid) => {
  const response = await axios.delete(`/api/archive/user/${uuid}`);
  return response.data;
};

export const updateUser = async (uuid, userData) => {
  const response = await axios.put(`/api/archive/user/${uuid}`, userData);
  return response.data;
};

export const getRelatedUsers = async () => {
  const response = await axios.get('/api/archive/user/related');
  return response.data;
}

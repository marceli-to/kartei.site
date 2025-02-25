import axios from 'axios';

export const getUser = async () => {
  const response = await axios.get('/api/user');
  return response.data;
};

export const updateUser = async (userData) => {
  const response = await axios.put(`/api/user`, userData);
  return response.data;
};
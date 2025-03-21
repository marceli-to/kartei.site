import axios from 'axios';

export const getArchives = async () => {
  const response = await axios.get('/api/archives');
  return response.data;
};

export const getAllArchives = async () => {
  const response = await axios.get('/api/archives/all');
  return response.data;
};

export const getByUser = async (userId) => {
  const response = await axios.get(`/api/archives/user/${userId}`);
  return response.data;
};
  
export const getArchivesByAdmin = async () => {
  const response = await axios.get('/api/archives/admin');
  return response.data;
};
  
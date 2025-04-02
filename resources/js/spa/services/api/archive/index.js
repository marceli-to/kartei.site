import axios from 'axios';

export const getArchive = async (uuid) => {
  const response = await axios.get(`/api/archive/${uuid}`);
  return response.data;
};

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

export const createArchive = async (archiveData) => {
  const response = await axios.post('/api/archive', archiveData);
  return response.data;
};

export const updateArchive = async (archiveData) => {
  const response = await axios.put(`/api/archive/${archiveData.uuid}`, archiveData);
  return response.data;
};
  
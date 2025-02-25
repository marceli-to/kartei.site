import axios from 'axios';

export const getArchives = async () => {
  const response = await axios.get('/api/archives');
  return response.data;
};

export const getUser = async () => {
  const response = await axios.get('/api/user');
  return response.data;
}
import axios from 'axios';

export const getArchives = async () => {
  const response = await axios.get('/api/archives');
  return response.data;
};
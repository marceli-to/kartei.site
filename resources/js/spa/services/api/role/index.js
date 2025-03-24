import axios from 'axios';

export const getRoles = async () => {
  const response = await axios.get('/api/roles');
  return response.data;
}

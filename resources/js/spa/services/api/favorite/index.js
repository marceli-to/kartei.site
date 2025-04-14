import api from '@/services/api/axios'

export const getFavorites = async () => {
  const response = await api.get('/favorites');
  return response.data;
}

export const toggleFavorite = async (uuid) => {
  const response = await api.get(`/favorite/toggle/${uuid}`);
  return response.data;
}

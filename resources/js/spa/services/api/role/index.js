import api from '@/services/api/axios'

export const getRoles = async () => {
  const response = await api.get('/roles');
  return response.data;
}

export const getRolesWithPermissions = async () => {
  const response = await api.get('/roles/permissions');
  return response.data;
}
  

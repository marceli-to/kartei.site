import api from '@/services/api/axios'

export const getPermissions = async () => {
  const response = await api.get('/permissions');
  return response.data;
}

export const getPermissionsByRole = async (role) => {
  const response = await api.get(`/permissions/role/${role}`);
  return response.data;
}

export const getPermissionsByUser = async (user) => {
  const response = await api.get(`/permissions/user/${user.uuid}`);
  return response.data;
};
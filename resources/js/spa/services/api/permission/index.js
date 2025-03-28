import axios from 'axios';

export const getPermissions = async () => {
  const response = await axios.get('/api/permissions');
  return response.data;
}

export const getPermissionsByRole = async (role) => {
  const response = await axios.get(`/api/permissions/role/${role}`);
  return response.data;
}

export const getPermissionsByUser = async (user) => {
  const response = await axios.get(`/api/permissions/user/${user.uuid}`);
  return response.data;
};
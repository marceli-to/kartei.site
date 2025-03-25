import axios from 'axios';

export const getRoles = async () => {
  const response = await axios.get('/api/roles');
  return response.data;
}

export const getRolesWithPermissions = async () => {
  const response = await axios.get('/api/roles/permissions');
  return response.data;
}
  

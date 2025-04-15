import api from '@/services/api/axios'

export const getArchive = async (uuid) => {
  const response = await api.get(`/archive/${uuid}`);
  return response.data;
};

export const getArchives = async () => {
  const response = await api.get('/archives');
  return response.data;
};

export const getAllArchives = async () => {
  const response = await api.get('/archives/all');
  return response.data;
};

export const getByUser = async (userId) => {
  const response = await api.get(`/archives/user/${userId}`);
  return response.data;
};
  
export const getArchivesByAdmin = async () => {
  const response = await api.get('/archives/admin');
  return response.data;
};

export const createArchive = async (archiveData) => {
  const response = await api.post('/archive', archiveData);
  return response.data;
};

export const updateArchive = async (archiveData) => {
  const response = await api.put(`/archive/${archiveData.uuid}`, archiveData);
  return response.data;
};

export const deleteArchive = async (uuid) => {
  const response = await api.delete(`/archive/${uuid}`);
  return response.data;
};

export const getArchiveSettings = async (uuid) => {
  const response = await api.get(`/archive/settings/${uuid}`);
  return response.data;
};

export const storeArchiveSettings = async (uuid, settings) => {
  const response = await api.put(`/archive/settings/${uuid}`, settings);
  return response.data;
};

export const deleteArchiveSettingsField = async (fieldUuid, archiveUuid) => {
  const response = await api.delete(`/archive/settings/field/${fieldUuid}/${archiveUuid}`);
  return response.data;
};

export const getArchiveMeta = async (uuid) => {
  const response = await api.get(`/archive/meta/${uuid}`);
  return response.data;
};


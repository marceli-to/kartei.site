import api from '@/services/api/axios'

export const getRecord = async (uuid) => {
  const response = await api.get(`/record/${uuid}`);
  return response.data;
};

export const getRecords = async (uuid) => {
  const response = await api.get(`/records/${uuid}`);
  return response.data;
};

export const createRecord = async (recordData) => {
  const response = await api.post('/record', recordData);
  return response.data;
};

export const updateRecord = async (recordData) => {
  const response = await api.put(`/record/${recordData.uuid}`, recordData);
  return response.data;
};

export const deleteRecord = async (uuid) => {
  const response = await api.delete(`/record/${uuid}`);
  return response.data;
};
import api from '@/services/api/axios'

export const getRecord = async (uuid) => {
  const response = await api.get(`/record/${uuid}`);
  return response.data;
};

// One page of records: { data: [...], meta: { current_page, last_page, per_page, total, archive_total } }
// params: { page, per_page, search, sort, categories: [], registers: [], tags: [] }
export const getRecords = async (uuid, params = {}, config = {}) => {
  const response = await api.get(`/records/${uuid}`, { params, ...config });
  return response.data;
};

export const createRecord = async (recordData) => {
  const response = await api.post('/record', recordData);
  return response.data;
};

export const updateRecord = async (uuid, recordData) => {
  const response = await api.put(`/record/${uuid}`, recordData);
  return response.data;
};

export const deleteRecord = async (uuid) => {
  const response = await api.delete(`/record/${uuid}`);
  return response.data;
};
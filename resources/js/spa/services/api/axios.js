import axios from 'axios';
import { handleApiError } from '@/services/api/error';

const api = axios.create({
  baseURL: '/api',
  timeout: 10000,
})

api.interceptors.response.use(
  response => response,
  error => {
    handleApiError(error, 'Fehler bei der Anfrage.')
    return Promise.reject(error)
  }
)

export default api

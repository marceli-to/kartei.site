import axios from 'axios';
import { handleApiError } from '@/services/api/error';
import { useGlobalLoading } from '@/composables/useGlobalLoading';

const api = axios.create({
  baseURL: '/api',
  timeout: 10000,
});

// get functions without instantiating twice
const { show, hide } = useGlobalLoading();

api.interceptors.request.use(
  (config) => {
    if (!(config.meta && config.meta.skipLoading)) {
      // mark this request as tracked so we know to hide later
      config.__usesGlobalLoading = true;
      show();
    }
    return config;
  },
  (error) => {
    // For request errors, we need to check if this request would have used loading
    // Since the error happens before __usesGlobalLoading is set, we check the same condition
    if (!(error.config?.meta && error.config.meta.skipLoading)) {
      hide();
    }
    return Promise.reject(error);
  }
);

api.interceptors.response.use(
  (response) => {
    if (response.config && response.config.__usesGlobalLoading) {
      hide();
    }
    return response;
  },
  (error) => {
    if (error.config && error.config.__usesGlobalLoading) {
      hide();
    }
    handleApiError(error, 'Fehler bei der Anfrage.');
    return Promise.reject(error);
  }
);

export default api;

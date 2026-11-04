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
  // No show() has run for a request that failed before being sent, so nothing to hide
  (error) => Promise.reject(error)
);

api.interceptors.response.use(
  (response) => {
    if (response.config && response.config.__usesGlobalLoading) {
      hide();
    }
    return response;
  },
  async (error) => {
    if (error.config && error.config.__usesGlobalLoading) {
      hide();
    }

    // Aborted on purpose (AbortController), nothing to report
    if (axios.isCancel(error)) {
      return Promise.reject(error);
    }

    // 419: the CSRF token is stale (e.g. the tab was open for a long time).
    // Fetch a fresh XSRF-TOKEN cookie and send the request once more, so the
    // form input survives. If the session itself is gone, the retry gets a 401.
    if (error.response?.status === 419 && error.config && !error.config.__retried419) {
      try {
        await axios.get('/sanctum/csrf-cookie');
        return await api({ ...error.config, __retried419: true });
      }
      catch (retryError) {
        // The retry went through this interceptor already
        if (retryError.config?.__retried419) return Promise.reject(retryError);
        error = retryError;
      }
    }

    handleApiError(error);
    return Promise.reject(error);
  }
);

export default api;

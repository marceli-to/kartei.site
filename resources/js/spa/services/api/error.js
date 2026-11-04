import { useToastStore } from '@/stores/toast';

let globalRouter = null

export function registerApiErrorRouter(routerInstance) {
  globalRouter = routerInstance
}

/**
 * Called by the axios interceptor for every failed request.
 *
 * - 401: the session is gone, show "Sitzung abgelaufen" (redirects to the login)
 * - 403/404 on a GET: the page cannot be shown, go to the error page
 * - otherwise: keep a message on the error (error.userMessage) for the view
 *
 * error.handled is set when the user was sent away, so views don't toast on top.
 */
export function handleApiError(error) {
  const status = error?.response?.status;
  const method = (error?.config?.method || 'get').toLowerCase();

  if (globalRouter && status === 401) {
    error.handled = true;
    globalRouter.push('/error/401');
    return;
  }

  if (globalRouter && method === 'get' && (status === 403 || status === 404)) {
    error.handled = true;
    globalRouter.push(`/error/${status}`);
    return;
  }

  error.userMessage = messageForStatus(error);
}

// A message only for statuses the view's own text doesn't cover better
function messageForStatus(error) {
  if (!error?.response) {
    return error?.code === 'ECONNABORTED'
      ? 'Der Server antwortet nicht. Bitte versuchen Sie es erneut.'
      : 'Keine Verbindung zum Server.';
  }

  switch (error.response.status) {
    case 403:
      return 'Dazu fehlt Ihnen die Berechtigung.';
    case 404:
      return 'Der Eintrag wurde nicht gefunden.';
    case 409:
      return 'Konflikt. Möglicherweise existiert der Eintrag bereits.';
    case 419:
      return 'Sitzung abgelaufen. Bitte laden Sie die Seite neu.';
    case 429:
      return 'Zu viele Anfragen. Bitte warten Sie einen Moment.';
    case 503:
      return 'Service derzeit nicht verfügbar.';
    default:
      return null;
  }
}

/**
 * Toast for a failed request in a view's catch block. Skipped when the
 * interceptor already sent the user to an error page or the request was
 * aborted on purpose; the status message
 * (no connection, no permission, ...) wins over the view's fallback text.
 */
export function notifyError(error, fallbackMessage = 'Ein unbekannter Fehler ist aufgetreten.') {
  if (error?.handled || error?.code === 'ERR_CANCELED') return;
  useToastStore().show(error?.userMessage || fallbackMessage, 'error');
}

import { ref } from 'vue';

/**
 * Validation errors of a form, one message per field.
 *
 * fields: ['name', 'email'], or { localKey: 'serverKey' } when the form uses
 * other names than the API (e.g. billing_city -> city).
 *
 *   const { errors, setErrors, clearErrors } = useFormErrors(['name', 'email']);
 *   try { ... } catch (error) { setErrors(error); }
 *   <InputLabel :error="errors.name" ... /> <InputText :error="errors.name" @update:error="errors.name = $event" ... />
 */
export function useFormErrors(fields) {
  const map = Array.isArray(fields)
    ? Object.fromEntries(fields.map(field => [field, field]))
    : fields;

  const empty = () => Object.fromEntries(Object.keys(map).map(key => [key, null]));
  const errors = ref(empty());

  const clearErrors = () => {
    errors.value = empty();
  };

  // Takes the first message per field from a Laravel 422 response; returns
  // whether there were any, so the caller can fall back to a generic toast
  const setErrors = (error) => {
    clearErrors();
    const serverErrors = error?.response?.data?.errors;
    if (!serverErrors) return false;
    for (const [key, serverKey] of Object.entries(map)) {
      errors.value[key] = serverErrors[serverKey]?.[0] ?? null;
    }
    return true;
  };

  return { errors, setErrors, clearErrors };
}

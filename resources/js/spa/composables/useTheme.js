import { ref, watch } from 'vue';
import { notifyError } from '@/services/api/error';
import { getUserTheme, updateUserTheme } from '@/services/api/user';

export function useTheme() {
  // true until the first fetch is done, so the form doesn't flash empty
  const isLoading = ref(true);
  const isSaving = ref(false);

  const theme = ref({
    color_scheme: '',
    color_theme: '',
  });

  // What the server has, to revert to when a save fails
  let savedTheme = { ...theme.value };
  // A change made while a save is running; saved right after it
  let pendingSave = false;

  // Color scheme options
  const colorSchemes = [
    { label: 'Hell', value: 'light' },
    { label: 'Dunkel', value: 'dark' },
  ];

  // Color theme options
  const colorThemes = [
    { label: 'Blau', value: 'ice' },
    { label: 'Pink', value: 'candy' },
    { label: 'Gelb', value: 'lemon' },
    { label: 'Grün', value: 'lime' },
  ];

  // Fetch user's theme from the API
  const fetchTheme = async () => {
    try {
      isLoading.value = true;
      const userThemeData = await getUserTheme();
      if (userThemeData.data) {
        theme.value.color_scheme = userThemeData.data.color_scheme;
        theme.value.color_theme = userThemeData.data.color_theme;
        savedTheme = { ...theme.value };

        // Apply the theme immediately when fetched
        updateTheme(theme.value.color_scheme, theme.value.color_theme);
      }
    } catch (error) {
      notifyError(error, 'Das Farbschema konnte nicht geladen werden.');
    } finally {
      isLoading.value = false;
    }
  };

  // Save user's theme to the API. Clicks during a save are not lost: the
  // latest choice is saved once the running request is done. A failed save
  // puts the last saved theme back.
  const saveTheme = async () => {
    if (isSaving.value) {
      pendingSave = true;
      return true;
    }

    isSaving.value = true;
    try {
      do {
        pendingSave = false;
        const values = { ...theme.value };
        await updateUserTheme(values);
        savedTheme = values;
      } while (pendingSave);
      return true;
    } catch (error) {
      pendingSave = false;
      theme.value.color_scheme = savedTheme.color_scheme;
      theme.value.color_theme = savedTheme.color_theme;
      notifyError(error, 'Das Farbschema konnte nicht gespeichert werden.');
      return false;
    } finally {
      isSaving.value = false;
    }
  };

  // Update the theme in the DOM using data attributes
  const updateTheme = (colorScheme, colorTheme) => {
    // Update color scheme data attribute
    document.documentElement.setAttribute('data-color-scheme', colorScheme || 'light');

    // Update color theme data attribute
    document.documentElement.setAttribute('data-color-theme', colorTheme || 'ice');
  };

  // Watch for changes to the theme and update the DOM
  watch(
    () => theme.value.color_scheme,
    (newColorScheme) => {
      updateTheme(newColorScheme, theme.value.color_theme);
    },
  );

  watch(
    () => theme.value.color_theme,
    (newColorTheme) => {
      updateTheme(theme.value.color_scheme, newColorTheme);
    },
  );

  return {
    theme,
    isLoading,
    isSaving,
    colorSchemes,
    colorThemes,
    fetchTheme,
    saveTheme,
    updateTheme,
  };
}

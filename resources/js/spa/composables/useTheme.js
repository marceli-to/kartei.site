import { ref, watch } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { getUserTheme, updateUserTheme } from '@/services/api/user';

export function useTheme() {
  const toast = useToastStore();
  
  const isLoading = ref(false);
  const isSaving = ref(false);
  
  const theme = ref({
    color_scheme: '',
    color_theme: ''
  });

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
    { label: 'Grün', value: 'lime' }
  ];
  
  // Fetch user's theme from the API
  const fetchTheme = async () => {
    try {
      isLoading.value = true;
      const userThemeData = await getUserTheme();
      if (userThemeData.data) {
        theme.value.color_scheme = userThemeData.data.color_scheme;
        theme.value.color_theme = userThemeData.data.color_theme;
        
        // Apply the theme immediately when fetched
        updateTheme(theme.value.color_scheme, theme.value.color_theme);
      }
    } 
    catch (error) {
      console.error('Error fetching theme:', error);
    } 
    finally {
      isLoading.value = false;
    }
  };
  
  // Save user's theme to the API
  const saveTheme = async () => {
    try {
      isSaving.value = true;
      await updateUserTheme(theme.value);
      toast.show('Einstellungen geändert.', 'success');
      return true;
    } 
    catch (error) {
      console.error('Error saving theme:', error);
      toast.show('Fehler beim Speichern.', 'error');
      return false;
    }
    finally {
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
  watch(() => theme.value.color_scheme, (newColorScheme) => {
    updateTheme(newColorScheme, theme.value.color_theme);
  });
  
  watch(() => theme.value.color_theme, (newColorTheme) => {
    updateTheme(theme.value.color_scheme, newColorTheme);
  });
  
  return {
    theme,
    isLoading,
    isSaving,
    colorSchemes,
    colorThemes,
    fetchTheme,
    saveTheme,
    updateTheme
  };
}

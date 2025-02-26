import { watch, computed } from 'vue';
import { useInfoIconStore } from '@/components/infobox/stores/info';

/**
 * Consolidated composable for managing the info box system
 * 
 * @param {Object} options - Configuration options
 * @param {Ref<boolean>|boolean} options.isActive - Component active state
 * @param {Ref<boolean>|boolean} options.condition - Condition that must be false to show info
 * @param {boolean} options.immediate - Whether to run the effect immediately (default: true)
 * @returns {Object} - Methods and components for the info box system
 */
export function useInfoBox(options) {
  const { 
    isActive, 
    condition, 
    immediate = true 
  } = options;
  
  // Get the info icon store
  const infoIcon = useInfoIconStore();
  
  // Convert values to refs if they aren't already
  const isActiveRef = typeof isActive === 'boolean' ? ref(isActive) : isActive;
  const conditionRef = typeof condition === 'boolean' ? ref(condition) : condition;
  
  // Function to update the icon state based on conditions
  const updateState = () => {
    const shouldActivate = isActiveRef.value && !conditionRef.value;
    infoIcon.setActive(shouldActivate);
    return shouldActivate;
  };
  
  // Watch for changes in either condition and update icon accordingly
  const stopWatch = watch(
    [isActiveRef, conditionRef],
    updateState,
    { immediate }
  );
  
  // Return everything needed to manage info boxes
  return {
    // Properties
    isActive: computed(() => infoIcon.isActive),
    
    // Methods
    updateState,
    hide: () => infoIcon.setActive(false),
    show: () => infoIcon.setActive(true),
    toggle: () => infoIcon.toggle(),
    
    // Cleanup
    stopWatch
  };
}
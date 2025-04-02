import { ref, computed, watch } from 'vue';
import { useUserStore } from '@/stores/user';

export function useSlider(initialSlides) {
  const userStore = useUserStore();
  const activeIndex = ref(0);
  const allSlidesRef = ref([...initialSlides]);
  
  // Filter sections based on permissions
  const slides = computed(() => {
    return allSlidesRef.value.filter(section =>
      !section.permission || userStore.can(section.permission)
    );
  });
  
  function handleSlideChange(newIndex) {
    if (newIndex >= 0 && newIndex < slides.value.length) {
      activeIndex.value = newIndex;
    }
  }
  
  // Function to update all slides
  function updateSlides(newSlides) {
    allSlidesRef.value = [...newSlides];
  }
  
  // Function to update a specific slide by ID
  function updateSlide(slideId, updatedProperties) {
    const index = allSlidesRef.value.findIndex(slide => slide.id === slideId);
    if (index !== -1) {
      allSlidesRef.value[index] = { 
        ...allSlidesRef.value[index], 
        ...updatedProperties 
      };
      
      // Create a new array to trigger reactivity
      allSlidesRef.value = [...allSlidesRef.value];
    }
  }
  
  return {
    activeIndex,
    slides,
    handleSlideChange,
    updateSlides,
    updateSlide
  };
}
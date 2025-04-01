import { ref, computed } from 'vue';
import { useUserStore } from '@/stores/user';

export function useSlider(allSlides) {
  const userStore = useUserStore();
  const activeIndex = ref(0);

  // Filter sections based on permissions
  const slides = computed(() => {
    return allSlides.filter(section =>
      !section.permission || userStore.can(section.permission)
    );
  });

  function handleSlideChange(newIndex) {
    if (newIndex >= 0 && newIndex < slides.value.length) {
      activeIndex.value = newIndex;
    }
  }

  return {
    activeIndex,
    slides,
    handleSlideChange
  };
}

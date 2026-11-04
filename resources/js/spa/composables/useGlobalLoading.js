import { ref, computed } from 'vue';

// Number of running requests/tasks; the overlay shows while it is above 0
const activeCount = ref(0);

function show() {
  activeCount.value += 1;
}

function hide() {
  if (activeCount.value <= 0) {
    return;
  }

  // immediately decrement count
  activeCount.value = Math.max(0, activeCount.value - 1);
}

export function useGlobalLoading() {
  const isActive = computed(() => activeCount.value > 0);

  // convenience helper for arbitrary async blocks
  async function withLoading(promiseLikeOrFn) {
    show();
    try {
      const p = typeof promiseLikeOrFn === 'function'
        ? promiseLikeOrFn()
        : promiseLikeOrFn;
      return await p;
    } finally {
      hide();
    }
  }

  return {
    isActive,
    show,
    hide,
    withLoading,
  };
}

import { ref, computed } from 'vue';

const activeCount = ref(0);
const minShowMs = 150; // avoid flicker on very fast ops
let visibleSince = 0;
let hideTimer = null;

function show() {
  if (hideTimer) {
    clearTimeout(hideTimer);
    hideTimer = null;
  }
  if (activeCount.value === 0) {
    visibleSince = performance.now();
  }
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

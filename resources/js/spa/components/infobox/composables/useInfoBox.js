import { watch, computed, toValue, onBeforeUnmount } from 'vue';
import { useInfoIconStore } from '@/components/infobox/stores/info';

let nextId = 0;

/**
 * Connects a view's info box to the lightbulb icon in the header.
 *
 * @param {Object} options
 * @param {Ref<boolean>|Function} options.active - The view is visible and has info to show
 * @param {Ref<boolean>|Function} [options.auto] - Open the box without a click,
 *   e.g. while something essential is missing
 * @returns {{ isOpen: ComputedRef<boolean> }} - Whether this view should show its box
 */
export function useInfoBox({ active, auto = false }) {
  const store = useInfoIconStore();
  const id = `info-${nextId++}`;

  watch(
    () => [toValue(active), toValue(auto)],
    ([isActive, isAuto]) => {
      isActive ? store.register(id, isAuto) : store.unregister(id);
    },
    { immediate: true }
  );

  onBeforeUnmount(() => store.unregister(id));

  return {
    isOpen: computed(() => toValue(active) && store.isOpen),
  };
}

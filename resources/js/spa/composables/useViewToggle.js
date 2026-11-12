import { ref, computed, watch } from 'vue';

// The bar above "Meine Karteien" and above the cards of an archive (Stackr #148): the same views
// Kacheln / Tabelle / Bilder, then Export and Teilen, greyed out until they exist.
// The chosen view is remembered in the browser under storageKey (one setting for all archives).
const VIEWS = [
  { value: 'tiles', label: 'Kacheln', icon: 'tiles' },
  { value: 'table', label: 'Tabelle', icon: 'list' },
  { value: 'images', label: 'Bilder', icon: 'image' },
];

const LATER = [
  { type: 'button', label: 'Export', icon: 'download', disabled: true },
  { type: 'button', label: 'Teilen', icon: 'upload', disabled: true },
];

const readStored = (key) => {
  try {
    return localStorage.getItem(key);
  } catch {
    return null;
  }
};

// disabledViews: getter for views that can't be chosen right now (e.g. ['images'] for an archive
// "Ohne Bildfeld"); they are greyed out and fall back to "tiles" without forgetting the stored choice.
export function useViewToggle(storageKey, disabledViews = () => []) {
  const stored = readStored(storageKey);
  const chosen = ref(VIEWS.some((view) => view.value === stored) ? stored : 'tiles');
  const layout = computed(() => (disabledViews().includes(chosen.value) ? 'tiles' : chosen.value));
  watch(chosen, (value) => {
    try {
      localStorage.setItem(storageKey, value);
    } catch {
      // Private mode or blocked storage: the view just isn't remembered
    }
  });

  const viewLinks = computed(() => [
    ...VIEWS.map((view) => ({
      type: 'button',
      label: view.label,
      icon: view.icon,
      active: layout.value === view.value,
      disabled: disabledViews().includes(view.value),
      onClick: () => (chosen.value = view.value),
    })),
    ...LATER,
  ]);

  // On the 10-column grid: tiles and images span 3 columns, a table row 2 + 3 (subgrid)
  const viewItemClasses = computed(() =>
    layout.value === 'table' ? 'md:col-span-2 lg:col-span-5 lg:col-start-1' : 'lg:col-span-3',
  );

  return { layout, viewLinks, viewItemClasses };
}

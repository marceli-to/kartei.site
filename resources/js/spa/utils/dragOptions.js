// Shared vuedraggable options for the sortable rows in the archive settings: <draggable v-bind="dragOptions" …>.
// The slider moves its slides with a CSS transform, and Safari takes the native drag image from the wrong
// spot inside a transformed element (only part of the row showed). forceFallback makes SortableJS draw its
// own copy of the row instead, attached to the body so the transform doesn't shift it either.
export const dragOptions = {
  ghostClass: 'drag-ghost',
  animation: 150,
  forceFallback: true,
  fallbackOnBody: true,
  // The copy is a cloneNode of the row, which keeps value attributes but not typed values: write the current
  // values into the attributes first (doesn't change what the fields show), so the copy shows the texts.
  onChoose: ({ item }) => {
    item.querySelectorAll('input').forEach((input) => input.setAttribute('value', input.value));
  },
};

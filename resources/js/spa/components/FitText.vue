<template>
  <!-- One line of text; if it doesn't fit, whole words are dropped from the end and "[…]" added
       ("Sammlung Bert […]"). CSS text-overflow only gives "…" mid-word in most browsers. -->
  <span
    ref="el"
    class="block min-w-0 overflow-hidden whitespace-nowrap"
    :title="shown !== text ? text : null"
    >{{ shown }}</span
  >
</template>

<script setup>
import { ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  text: {
    type: String,
    default: '',
  },
});

const el = ref(null);
const shown = ref(props.text);

const fits = () => el.value.scrollWidth <= el.value.clientWidth;

const fit = async () => {
  if (!el.value) return;
  const words = props.text.split(' ');
  shown.value = props.text;
  await nextTick();
  let count = words.length;
  while (!fits() && count > 1) {
    count--;
    shown.value = `${words.slice(0, count).join(' ')} […]`;
    await nextTick();
  }
};

let observer = null;
let lastWidth = 0;
onMounted(() => {
  fit();
  // Refit when the available width changes (window resize), not when the text shortens itself
  observer = new ResizeObserver(([entry]) => {
    if (entry.contentRect.width !== lastWidth) {
      lastWidth = entry.contentRect.width;
      fit();
    }
  });
  observer.observe(el.value);
});
onBeforeUnmount(() => observer?.disconnect());

watch(() => props.text, fit);
</script>

<template>
  <!--
    Large view of a record's images (KAR0001_Desktop_261111, p. 8): a square white box 40px from the
    window edges on a dark backdrop, the image 80px inside the box, thin arrows 20px from the left/right edge
    (only with several images, wrapping around) and a thin cross 20px from the top right.
    Closes with the cross, Esc or a click on the backdrop; arrow keys go back and forth.
    The page doesn't scroll behind it: wheel and touch scrolling are swallowed here instead of hiding
    the page's overflow, which would remove the scroll bar and make the page jump.
  -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-to-class="opacity-0">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-9999 flex items-center justify-center bg-black/80"
        @click.self="close"
        @wheel.prevent
        @touchmove.prevent>
        <div
          ref="panel"
          tabindex="-1"
          role="dialog"
          aria-modal="true"
          aria-label="Bildansicht"
          class="relative size-[min(calc(100vh-80px),calc(100vw-80px))] bg-white outline-none dark:bg-black">
          <Image
            v-if="current"
            :key="current.src"
            :src="current.src"
            :alt="current.alt || `Bild ${index + 1} von ${images.length}`"
            :width="current.width"
            :height="current.height"
            spacing="m-48 lg:m-80" />

          <template v-if="images.length > 1">
            <button
              type="button"
              aria-label="Vorheriges Bild"
              class="absolute top-1/2 left-20 flex -translate-y-1/2 items-center justify-center py-12"
              @click="previous">
              <Icon
                name="chevron-left"
                variant="thin"
                class="h-29 w-16" />
            </button>
            <button
              type="button"
              aria-label="Nächstes Bild"
              class="absolute top-1/2 right-20 flex -translate-y-1/2 items-center justify-center py-12"
              @click="next">
              <Icon
                name="chevron-right"
                variant="thin"
                class="h-29 w-16" />
            </button>
          </template>

          <button
            type="button"
            aria-label="Schliessen"
            class="absolute top-20 right-20"
            @click="close">
            <Icon
              name="cross"
              variant="thin"
              class="size-24" />
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';
import Image from '@/components/media/Image.vue';
import Icon from '@/components/icons/Icon.vue';

const props = defineProps({
  // [{ src, alt?, width?, height? }]
  images: {
    type: Array,
    required: true,
  },
});

// Index of the image shown; null = closed
const index = defineModel({ type: Number, default: null });

const isOpen = computed(() => index.value !== null && props.images.length > 0);
const current = computed(() => (isOpen.value ? props.images[index.value] : null));

const panel = ref(null);
let returnFocusTo = null;

const close = () => (index.value = null);
const previous = () => (index.value = (index.value - 1 + props.images.length) % props.images.length);
const next = () => (index.value = (index.value + 1) % props.images.length);

const onKeydown = (event) => {
  if (event.key === 'Escape') close();
  else if (event.key === 'ArrowLeft' && props.images.length > 1) previous();
  else if (event.key === 'ArrowRight' && props.images.length > 1) next();
};

// Keyboard and focus only while open
watch(isOpen, async (open) => {
  if (open) {
    returnFocusTo = document.activeElement;
    window.addEventListener('keydown', onKeydown);
    await nextTick();
    panel.value?.focus();
  } else {
    window.removeEventListener('keydown', onKeydown);
    returnFocusTo?.focus?.();
    returnFocusTo = null;
  }
});

onUnmounted(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
  <div
    class="pointer-events-none fixed inset-x-0 top-0 z-[9999] h-2 select-none transition-opacity duration-200 motion-reduce:transition-none"
    :class="isVisible ? 'opacity-100' : 'opacity-0'"
    role="progressbar"
    aria-label="Wird geladen"
    :aria-hidden="!isVisible">
    <div
      class="theme-color h-full w-full origin-left transition-transform duration-200 ease-out motion-reduce:transition-none"
      :style="{ transform: `scaleX(${progress})` }" />
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { useGlobalLoading } from '@/composables/useGlobalLoading';

const { isActive } = useGlobalLoading();

// Fast requests finish before the bar shows, so it doesn't flicker
const SHOW_DELAY = 150;
// How long the full bar stays before it fades out
const FINISH_DELAY = 200;
// The bar creeps towards this while loading; it only reaches the end when done
const MAX_PROGRESS = 0.9;

const isVisible = ref(false);
const progress = ref(0);

let showTimer = null;
let trickleTimer = null;
let finishTimer = null;

function clearTimers() {
  clearTimeout(showTimer);
  clearInterval(trickleTimer);
  clearTimeout(finishTimer);
  showTimer = trickleTimer = finishTimer = null;
}

function start() {
  clearTimeout(finishTimer);
  finishTimer = null;

  // Still on screen from the last load: keep going from where it is
  if (isVisible.value) {
    progress.value = Math.min(progress.value, MAX_PROGRESS);
    trickle();
    return;
  }

  showTimer = setTimeout(() => {
    showTimer = null;
    isVisible.value = true;
    progress.value = 0.1;
    trickle();
  }, SHOW_DELAY);
}

// Slower the closer it gets, so a long load never looks finished
function trickle() {
  clearInterval(trickleTimer);
  trickleTimer = setInterval(() => {
    progress.value += (MAX_PROGRESS - progress.value) * 0.15;
  }, 300);
}

function finish() {
  clearTimeout(showTimer);
  showTimer = null;
  clearInterval(trickleTimer);
  trickleTimer = null;

  if (!isVisible.value) return;

  progress.value = 1;
  finishTimer = setTimeout(() => {
    finishTimer = null;
    isVisible.value = false;
    // Reset once faded out, so the next load starts from the left
    setTimeout(() => {
      if (!isVisible.value) progress.value = 0;
    }, 200);
  }, FINISH_DELAY);
}

watch(isActive, (active) => (active ? start() : finish()), { immediate: true });

onBeforeUnmount(clearTimers);
</script>

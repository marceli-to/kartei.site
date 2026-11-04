<template>
  <div class="flex flex-grow w-full overflow-hidden mt-48 relative">

    <nav class="w-2/12 min-w-nav sticky shrink-0 left-0 mt-58 min-h-full z-50 border-graphite border-r bg-white dark:bg-black">
      <div :class="{ 'border border-graphite min-h-[calc(100%_-_20px)] mr-8 pl-8': navigationVariant === 'box' }">

        <!-- Previous slide, covers the headings that slid under the navigation -->
        <div
          v-if="activeIndex > 0"
          class="absolute left-0 -top-60 w-full flex items-center justify-between bg-white dark:bg-black">
          <h1 class="opacity-20 font-muoto-medium">
            {{ visibleSlides[activeIndex - 1].name }}
          </h1>
          <button
            type="button"
            @click="prev"
            title="Vorheriger Abschnitt"
            aria-label="Vorheriger Abschnitt"
            class="opacity-20 mr-16">
            <Icon name="chevron-left" variant="small-bold" />
          </button>
        </div>

        <slot name="header" />

        <ul>
          <li
            v-for="item in visibleSlides"
            :key="item.id"
            class="min-h-default flex items-center border-b border-b-graphite first-of-type:border-t first-of-type:border-t-graphite mr-8">
            <button
              type="button"
              @click="go(item.id)"
              :disabled="item.disabled"
              :aria-current="item.id === activeId ? 'step' : undefined"
              class="block w-full text-left disabled:opacity-50"
              :class="{ 'font-muoto-medium': item.id === activeId }">
              {{ item.name }}
            </button>
          </li>
        </ul>
      </div>
    </nav>

    <div
      ref="track"
      class="flex flex-grow w-full relative z-40"
      :class="{ 'transition-transform duration-300 ease-in-out': animate }"
      :style="{ transform: `translateX(-${offset}px)` }">
      <section
        v-for="(item, index) in visibleSlides"
        :key="item.id"
        :ref="el => setSectionEl(item.id, el)"
        :class="item.class"
        class="shrink-0">
        <h2
          class="ml-8 flex justify-between items-center font-muoto-medium leading-none h-20"
          :class="{ 'opacity-20': index !== activeIndex }">
          <span>{{ item.name }}</span>
          <button
            v-if="index >= activeIndex && index < visibleSlides.length - 1"
            type="button"
            @click="next"
            title="Nächster Abschnitt"
            aria-label="Nächster Abschnitt"
            class="mr-16">
            <Icon name="chevron-right" variant="small-bold" />
          </button>
        </h2>
        <div class="flex flex-grow min-h-full pt-38">
          <!-- inert: no clicks and no keyboard focus outside the active slide -->
          <div
            class="flex-grow min-h-full border-r border-r-graphite relative"
            :class="{
              'opacity-20 transition-opacity': index > activeIndex,
              'opacity-0 transition-opacity': index < activeIndex,
            }"
            :inert="index !== activeIndex">
            <component
              :is="item.component"
              v-bind="item.props || {}"
              :isActive="index === activeIndex"
              class="shrink-0" />
          </div>
        </div>
      </section>
    </div>

  </div>
</template>

<script setup>
/**
 * Horizontal slider for the settings pages.
 *
 * slides: [{ id, name, class, component, props?, permission?, disabled? }]
 * - props: extra props for the slide's component (it always gets isActive)
 * - class: width classes of the slide, e.g. "w-3/12 min-w-3/12"
 * - permission: a global permission name, or a function returning a boolean
 *   (e.g. a per-archive check via userStore.canArchive). Hidden when not granted.
 * - disabled: a boolean or a function returning one. Shown, but not selectable.
 *
 * The active slide is kept in the query string (?section=<id>), so it survives
 * a reload and can be linked to directly.
 */
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useUserStore } from '@/stores/user';
import Icon from '@/components/icons/Icon.vue';

const props = defineProps({
  slides: {
    type: Array,
    default: () => []
  },
  navigationVariant: {
    type: String,
    default: ''
  }
});

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

const resolve = (value) => (typeof value === 'function' ? value() : value);

const visibleSlides = computed(() =>
  props.slides
    .filter(slide => {
      if (!slide.permission) return true;
      return typeof slide.permission === 'function'
        ? slide.permission()
        : userStore.can(slide.permission);
    })
    .map(slide => ({ ...slide, disabled: !!resolve(slide.disabled) }))
);

const isSelectable = (id) => visibleSlides.value.some(slide => slide.id === id && !slide.disabled);
const firstSelectable = () => visibleSlides.value.find(slide => !slide.disabled)?.id ?? null;

const activeId = ref(isSelectable(route.query.section) ? route.query.section : firstSelectable());
const activeIndex = computed(() => visibleSlides.value.findIndex(slide => slide.id === activeId.value));

function go(id) {
  if (!isSelectable(id) || id === activeId.value) return;
  activeId.value = id;
  router.replace({ query: { ...route.query, section: id } });
}

function step(direction) {
  const target = visibleSlides.value[activeIndex.value + direction];
  if (target) go(target.id);
}

const next = () => step(1);
const prev = () => step(-1);

// Links to another section while the page is open
watch(() => route.query.section, (section) => {
  if (section && section !== activeId.value && isSelectable(section)) {
    activeId.value = section;
  }
});

// The active slide got hidden or disabled (permissions, new archive)
watch(visibleSlides, () => {
  if (!isSelectable(activeId.value)) {
    activeId.value = firstSelectable();
  }
});

// Position of the track: the active slide's left edge
const track = ref(null);
const sectionEls = new Map();
const offset = ref(0);
const animate = ref(false);

function setSectionEl(id, el) {
  if (el) sectionEls.set(id, el);
  else sectionEls.delete(id);
}

function measure(withTransition) {
  animate.value = withTransition;
  offset.value = sectionEls.get(activeId.value)?.offsetLeft ?? 0;
}

watch(activeId, () => nextTick(() => measure(true)));
watch(visibleSlides, () => nextTick(() => measure(false)));

let resizeObserver = null;
let trackWidth = null;

onMounted(() => {
  measure(false);

  // Only width changes move the slides. Height changes happen on every slide
  // change (e.g. the save button of the active slide) and would cut the
  // transition short.
  resizeObserver = new ResizeObserver(([entry]) => {
    const width = entry.contentRect.width;
    if (width === trackWidth) return;
    trackWidth = width;
    measure(false);
  });
  resizeObserver.observe(track.value);
});

onBeforeUnmount(() => {
  resizeObserver?.disconnect();
});
</script>

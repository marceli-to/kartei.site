<template>
  <!--
    Vertical layout, shared by the navigation and every slide:
    heading row (h-20), gap (mt-38), then the content line, where the navigation box
    and the slides start (slides with a label above that line use Slide's "pull").
    Slides end pb-20 above the bottom edge (Slide.vue).
  -->
  <div class="relative mt-48 flex w-full flex-grow overflow-hidden">
    <nav class="sticky left-0 z-50 flex w-2/12 min-w-nav shrink-0 flex-col bg-white dark:bg-black">
      <!-- Previous slide, covers the headings that slid under the navigation -->
      <div class="flex h-20 shrink-0 items-center justify-between">
        <template v-if="activeIndex > 0">
          <h1 class="font-muoto-medium opacity-20">
            {{ visibleSlides[activeIndex - 1].name }}
          </h1>
          <button
            type="button"
            title="Vorheriger Abschnitt"
            aria-label="Vorheriger Abschnitt"
            class="mr-16 opacity-20"
            @click="prev">
            <Icon
              name="chevron-left"
              variant="small-bold" />
          </button>
        </template>
      </div>

      <div class="mt-38 flex flex-grow flex-col border-r border-graphite">
        <div
          class="flex flex-col"
          :class="{ 'mb-20 mr-8 flex-grow border border-graphite pl-8': navigationVariant === 'box' }">
          <slot name="header" />

          <ul>
            <li
              v-for="item in visibleSlides"
              :key="item.id"
              class="mr-8 flex min-h-default items-center border-b border-b-graphite first-of-type:border-t first-of-type:border-t-graphite">
              <button
                type="button"
                :disabled="item.disabled"
                :aria-current="item.id === activeId ? 'step' : undefined"
                class="block w-full text-left disabled:opacity-50"
                :class="{ 'font-muoto-medium': item.id === activeId }"
                @click="go(item.id)">
                {{ item.name }}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div
      ref="track"
      class="relative z-40 flex w-full flex-grow"
      :class="{ 'transition-transform duration-300 ease-in-out': animate }"
      :style="{ transform: `translateX(-${offset}px)` }">
      <section
        v-for="(item, index) in visibleSlides"
        :key="item.id"
        :ref="(el) => setSectionEl(item.id, el)"
        :class="item.class"
        class="flex shrink-0 flex-col">
        <h2
          class="ml-8 flex h-20 items-center justify-between font-muoto-medium leading-none"
          :class="{ 'opacity-20': index !== activeIndex }">
          <span>{{ item.name }}</span>
          <!-- Only the active slide's button is a Tab stop; the ones on the following slides are a mouse shortcut -->
          <button
            v-if="canGoNext && index >= activeIndex && index < visibleSlides.length - 1"
            type="button"
            title="Nächster Abschnitt"
            aria-label="Nächster Abschnitt"
            :tabindex="index === activeIndex ? undefined : -1"
            :aria-hidden="index === activeIndex ? undefined : 'true'"
            class="mr-16"
            @click="next">
            <Icon
              name="chevron-right"
              variant="small-bold" />
          </button>
        </h2>
        <!-- inert: no clicks and no keyboard focus outside the active slide -->
        <div
          class="relative mt-38 flex flex-grow flex-col border-r border-r-graphite"
          :class="{
            'opacity-20 transition-opacity': index > activeIndex,
            'opacity-0 transition-opacity': index < activeIndex,
          }"
          :inert="index !== activeIndex">
          <component
            :is="item.component"
            v-bind="item.props || {}"
            :is-active="index === activeIndex"
            class="shrink-0" />
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
    default: () => [],
  },
  navigationVariant: {
    type: String,
    default: '',
  },
});

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

const resolve = (value) => (typeof value === 'function' ? value() : value);

const visibleSlides = computed(() =>
  props.slides
    .filter((slide) => {
      if (!slide.permission) return true;
      return typeof slide.permission === 'function' ? slide.permission() : userStore.can(slide.permission);
    })
    .map((slide) => ({ ...slide, disabled: !!resolve(slide.disabled) })),
);

const isSelectable = (id) => visibleSlides.value.some((slide) => slide.id === id && !slide.disabled);
const firstSelectable = () => visibleSlides.value.find((slide) => !slide.disabled)?.id ?? null;

const activeId = ref(isSelectable(route.query.section) ? route.query.section : firstSelectable());
const activeIndex = computed(() => visibleSlides.value.findIndex((slide) => slide.id === activeId.value));
const canGoNext = computed(() => isSelectable(visibleSlides.value[activeIndex.value + 1]?.id));

// A link to a hidden or disabled section shows another slide; keep the URL in line
function syncQuery() {
  if (route.query.section && route.query.section !== activeId.value) {
    router.replace({ query: { ...route.query, section: activeId.value ?? undefined } });
  }
}
syncQuery();

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
watch(
  () => route.query.section,
  (section) => {
    if (!section || section === activeId.value) return;
    if (isSelectable(section)) activeId.value = section;
    else syncQuery();
  },
);

// The active slide got hidden or disabled (permissions, new archive)
watch(visibleSlides, () => {
  if (!isSelectable(activeId.value)) {
    activeId.value = firstSelectable();
    syncQuery();
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

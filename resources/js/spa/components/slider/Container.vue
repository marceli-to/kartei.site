<template>
  <div class="flex flex-grow w-full overflow-hidden mt-48 relative">

    <SliderNavigation :variant="navigationVariant">
      <template #title
        v-if="activeIndex > 0">
        <div class="absolute left-0 -top-60 w-full flex items-center justify-between bg-white dark:bg-black">
          <h1 class="opacity-20 font-muoto-medium">
            {{ previousSectionName }}
          </h1>
          <a 
            href="javascript:;"
            @click.prevent="prev"
            title="Prev Section"
            class="opacity-20">
            <IconChevronLeft variant="small" />
          </a>
        </div>
      </template>
      <template #navigationTitle>
        <slot name="navigationTitle"></slot>
      </template>
      <template #navigation>
        <ul>
          <li 
            v-for="(item, index) in slides" :key="index" 
            class="min-h-default flex items-center border-b border-b-graphite first-of-type:border-t first-of-type:border-t-graphite mr-8">
            <a 
              href="javascript:;"
              @click="activeIndex = index"
              class="block w-full"
              :class="{
                'font-muoto-medium': activeIndex === index,
                '!opacity-50 pointer-events-none': item.disabled,
              }">
              {{ item.name }}
            </a>
          </li>
        </ul>
      </template>
    </SliderNavigation>
    
    <SliderMain :style="{ transform: `translateX(-${computedOffsets[activeIndex]}%)` }">
      <section 
        v-for="(item, index) in slides" 
        :key="index" 
        :class="item.class"
        class="shrink-0">
        <h2 
          class="ml-8 opacity-1 flex justify-between items-center font-muoto-medium leading-none h-20"
          :class="{
            'transition-none delay-300': activeIndex === index, 
            'opacity-20': activeIndex !== index,
            'opacity-0 transition-all duration-none': activeIndex > index
          }">
          <span>{{ item.name }}</span>
          <template v-if="index >= activeIndex && index < slides.length - 1">
            <a 
              href="javascript:;"
              @click.prevent="next"
              title="Next Section"
              class="mr-8">
              <IconChevronRight variant="small" />
            </a>
          </template>
        </h2>
        <div class="flex flex-grow min-h-full pt-38">
          <div 
            class="flex-grow min-h-full border-r border-r-graphite relative"
            :class="{ 
              'opacity-20 pointer-events-none transition-all duration-none': activeIndex < index,
              'opacity-0 pointer-events-none transition-all duration-none': activeIndex > index
            }">
            <component 
              v-if="item.component" 
              :is="item.component" 
              :componentProps="item.props || null"
              :isActive="activeIndex === index" 
              class="shrink-0" />
            <slot v-else :name="`section-${index}`">
              No content provided for section {{ item.name }}
            </slot>
          </div>
        </div>
      </section>
    </SliderMain>

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import IconChevronRight from '@/components/icons/ChevronRight.vue';
import IconChevronLeft from '@/components/icons/ChevronLeft.vue';
import SliderNavigation from '@/components/slider/Navigation.vue';
import SliderMain from '@/components/slider/Main.vue';

const props = defineProps({
  slides: {
    type: Array,
    default: () => []
  },
  initialActiveIndex: {
    type: Number,
    default: 0
  },
  navigationVariant: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['slide-change']);

const activeIndex = ref(props.initialActiveIndex);

watch(activeIndex, (newIndex) => {
  emit('slide-change', newIndex);
});

const computedOffsets = computed(() => {
  const offsets = [];
  let sum = 0;
  props.slides.forEach((slide) => {
    offsets.push(sum);
    sum += slide.width;
  });
  return offsets;
});

const previousSectionName = computed(() => {
  return activeIndex.value > 0 ? props.slides[activeIndex.value - 1].name : '';
});

const next = () => {
  if (activeIndex.value < props.slides.length - 1) {
    activeIndex.value++;
  }
}

const prev = () => {
  if (activeIndex.value > 0) {
    activeIndex.value--;
  }
}

defineExpose({
  activeIndex,
  next,
  prev
});
</script>